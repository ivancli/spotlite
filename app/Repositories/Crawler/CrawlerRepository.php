<?php
namespace App\Repositories\Crawler;

use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Events\Products\Crawler\CrawlerFinishing;
use App\Events\Products\Crawler\CrawlerLoadingHTML;
use App\Events\Products\Crawler\CrawlerLoadingPrice;
use App\Events\Products\Crawler\CrawlerRunning;
use App\Events\Products\Crawler\CrawlerSavingPrice;
use App\Models\Crawler;
use App\Models\Domain;
use App\Models\HistoricalPrice;
use Illuminate\Support\Facades\Cache;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 10:48 AM
 */
class CrawlerRepository implements CrawlerContract
{
    public function getCrawlers()
    {
        $crawlers = Crawler::all();
        return $crawlers;
    }

    public function getCrawler($crawler_id)
    {
        $crawler = Crawler::findOrFail($crawler_id);
        return $crawler;
    }

    public function updateCrawler($crawler_id, $options)
    {
        $crawler = $this->getCrawler($crawler_id);
        $crawler->update($options);
        return $crawler;
    }

    public function deleteCrawler($crawler_id)
    {
        // TODO: Implement deleteCrawler() method.
    }

    public function pickCrawler()
    {
//        DB::enableQueryLog();

        //SELECT TIMESTAMPDIFF(HOUR, DATE_FORMAT(last_active_at, "%Y-%m-%d %H:00:00"), NOW()) FROM crawlers

        /* ignore the record which has crawled within an hour */
        $crawler = Crawler::whereNull("status")->whereRaw('(last_active_at IS NULL OR TIMESTAMPDIFF(HOUR, DATE_FORMAT(last_active_at, "%Y-%m-%d %H:00:00"), NOW()) != 0) ')->first();
//        dd(DB::getQueryLog());

        if (!is_null($crawler)) {
            $crawler->pick();
            $crawler->updateLastActiveAt();
        }
        return $crawler;
    }

    public function setCrawlerQueuing($crawler_id)
    {
        $crawler = $this->getCrawler($crawler_id);
        $crawler->status = "queuing";
        $crawler->save();
        return $crawler;
    }

    public function setCrawlerRunning($crawler_id)
    {
        $crawler = $this->getCrawler($crawler_id);
        $crawler->status = "queuing";
        $crawler->save();
        return $crawler;
    }

    public function crawl(Crawler $crawler, CrawlerInterface $crawlerClass, ParserInterface $parserClass)
    {
        /*TODO check once again to prevent duplication*/

        if (!$crawler->lastCrawlerWithinHour()) {
            return false;
        }
        event(new CrawlerRunning($crawler));
        $crawler->status = "running";
        $crawler->save();

        $site = $crawler->site;
        $options = array(
            "url" => $site->site_url,
        );
        $crawlerClass->setOptions($options);
        event(new CrawlerLoadingHTML($crawler));

        /*check cache*/
        if (Cache::tags(['crawled_sites'])->has($site->site_url)) {
            $html = Cache::tags(['crawled_sites'])->get($site->site_url);
        } else {
            $crawlerClass->loadHTML();
            $html = $crawlerClass->getHTML();

            Cache::tags(['crawled_sites'])->put($site->site_url, $html, 60);
        }


        if (is_null($html) || strlen($html) == 0) {
            /*TODO handle error, page not crawled*/
            $site->statusFailHTML();
        }
//        if (is_null($xpath)) {
//            $domain = Domain::where('domain_url', $site->domain)->first();
//            if (!is_null($domain)) {
//                $xpath = $domain->domain_xpath;
//            }
//        }
        for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
            $xpath = $site->preference->toArray()["xpath_{$xpathIndex}"];
            if ($xpath != null) {
                $options = array(
                    "xpath" => $xpath,
                );
                $parserClass->setOptions($options);
                $parserClass->setHTML($html);
                $parserClass->init();
                event(new CrawlerLoadingPrice($crawler));
                $result = $parserClass->parseHTML();
                if (!is_null($result) && (is_string($result) || is_numeric($result))) {
                    $price = $result;
                    foreach(config("constants.price_describers") as $priceDescriber){
                        $price = str_replace($priceDescriber, '', $price);
                    }

                    $price = floatval($price);
                    if ($price > 0) {
                        /*TODO now you got the $price*/

                        $historicalPrice = HistoricalPrice::create(array(
                            "crawler_id" => $crawler->getKey(),
                            "site_id" => $site->getKey(),
                            "price" => $price
                        ));

                        if (!is_null($site->recent_price)) {
                            $site->price_diff = $price - $site->recent_price;
                        }
                        $site->recent_price = $price;
                        $site->last_crawled_at = $historicalPrice->created_at;

                        if (!$crawler->lastCrawlerWithinHour()) {
                            return false;
                        }
                        event(new CrawlerSavingPrice($crawler));
                        $site->save();
                        $site->statusOK();
                        event(new CrawlerFinishing($crawler));
                        $crawler->resetStatus();
                        return true;
                    } else {
                        /*TODO handle error, price is incorrect*/
                        $site->statusFailPrice();
                        continue;
                    }
                } else {
                    /*TODO handle error, xpath is incorrect*/
                    $site->statusFailXpath();
                    continue;
                }
            } else {
                /*TODO handle error, cannot find xpath*/
                if ($xpathIndex == 1) {
                    $site->statusNullXpath();
                }
                break;
            }
        }
        $crawler->resetStatus();
        return false;
    }
}