<?php
namespace App\Repositories\Crawler;

use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Events\Products\Crawler\CrawlerFinishing;
use App\Events\Products\Crawler\CrawlerLoadingHTML;
use App\Events\Products\Crawler\CrawlerRunning;
use App\Events\Products\Crawler\CrawlerSavingPrice;
use App\Jobs\SendMail;
use App\Models\Crawler;
use App\Models\HistoricalPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\CurrencyFormatterInterface;
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
        $crawler->status = "running";
        $crawler->save();
        return $crawler;
    }

    public function crawl(Crawler $crawler, CrawlerInterface $crawlerClass, ParserInterface $parserClass, CurrencyFormatterInterface $currencyFormatterClass = null)
    {
//        /*TODO check once again to prevent duplication*/
//        if ($crawler->lastCrawlerWithinHour()) {
//            return false;
//        }

        event(new CrawlerRunning($crawler));

        $crawler->run();

        $site = $crawler->site;

        if ($site->status == 'invalid' || $site->status == 'sample') {
            $crawler->resetStatus();
            unset($crawler, $site);
            return false;
        }

        event(new CrawlerLoadingHTML($crawler));

        /*check cache*/
        $content = Cache::tags(['crawlers'])->remember("{$site->site_url}.content", 60, function () use ($site, $crawlerClass) {
            $options = array(
                "url" => $site->site_url,
            );
            return $this->crawlPage($options, $crawlerClass);
        });

        File::put(storage_path('crawler/' . $site->getKey()), $content);

        // page cannot be crawled
        if (is_null($content) || strlen($content) == 0) {
            $site->statusFailHTML();
            $crawler->resetStatus();
            unset($crawler, $site);
            unset($content);
            return false;
        }

        for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
            $xpath = $site->preference->toArray()["xpath_{$xpathIndex}"];
            if ($xpath != null || (!is_null($crawler->crawler_class) || !is_null($crawler->parser_class))) {
                $result = $this->parserPrice($xpath, $content, $parserClass, $currencyFormatterClass);
                if (isset($result['status']) && $result['status'] == true) {
                    $price = $result['price'];
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

                    event(new CrawlerSavingPrice($crawler));
                    $site->save();
                    $site->statusOK();
                    event(new CrawlerFinishing($crawler));
                    $crawler->resetStatus();

                    unset($crawler, $site, $historicalPrice);
                    unset($xpath, $result, $price);

                    return true;
                } else {

                    $status = false;
                    if (isset($result['error'])) {
                        if ($site->status == "no_price") {
                            $site->statusNoPrice();
                            unset($result, $xpath);
                            continue;
                        } elseif ($result['error'] == "incorrect price") {
                            $site->statusFailPrice();
                            unset($result, $xpath);
                            continue;
                        } elseif ($result['error'] == "incorrect xpath") {
                            $site->statusFailXpath();
                            unset($result, $xpath);
                            continue;
                        }
                    }

                    event(new CrawlerFinishing($crawler));
                }
            } else {
                /*TODO handle error, cannot find xpath*/

                if ($xpathIndex == 1) {
                    $site->statusNullXpath();
                }
                event(new CrawlerFinishing($crawler));
                break;
            }
        }

        $crawler->resetStatus();
        event(new CrawlerFinishing($crawler));

        unset($crawler, $site);
        return false;
    }

    public function crawlPage($options, CrawlerInterface $crawlerClass)
    {
        $crawlerClass->setOptions($options);
        $crawlerClass->loadHTML();
        $html = $crawlerClass->getHTML();

        unset($crawlerClass);

        return $html;
    }

    public function parserPrice($xpath, $content, ParserInterface $parserClass, CurrencyFormatterInterface $currencyFormatterClass = null)
    {
        $options = array(
            "xpath" => $xpath,
        );
        $parserClass->setOptions($options);

        unset($options);

        $parserClass->setHTML($content);
        $parserClass->init();
        $result = $parserClass->parseHTML();

        unset($parserClass);

        if (!is_null($result) && (is_string($result) || is_numeric($result))) {
            $price = $result;
            unset($result);
            $price = utf8_decode($price);
            if (!is_null($currencyFormatterClass)) {
                $currencyFormatterClass->setPriceText($price);
                $currencyFormatterClass->formatPriceText();
                $price = $currencyFormatterClass->getPriceText();
                unset($currencyFormatterClass);
            }
            $price = preg_replace('@[^0-9\.]+@i', '', $price);
            foreach (config("constants.price_describers") as $priceDescriber) {
                $price = str_replace($priceDescriber, '', $price);
            }
            $price = floatval($price);
            if ($price > 0) {
                /*correct price*/
                $status = true;
                return compact(['status', 'price']);
            } else {
                /*incorrect price*/
                $status = false;
                $error = "incorrect price";
                return compact('status', 'error');
            }
        } else {
            unset($result);

            /*crawled content is not a price*/
            $status = false;
            $error = "incorrect xpath";
            return compact(['status', 'error']);
        }
    }
}