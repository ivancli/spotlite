<?php
namespace App\Repositories\Crawler;

use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Events\Products\Crawler\CrawlerFinishing;
use App\Events\Products\Crawler\CrawlerLoadingHTML;
use App\Events\Products\Crawler\CrawlerRunning;
use App\Events\Products\Crawler\CrawlerSavingPrice;
use App\Models\Crawler;
use App\Models\HistoricalPrice;
use Illuminate\Support\Facades\Cache;
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
        /*TODO check once again to prevent duplication*/

        if (!$crawler->lastCrawlerWithinHour()) {
            return false;
        }
        event(new CrawlerRunning($crawler));
        $this->setCrawlerRunning($crawler->getKey());

        $site = $crawler->site;

        if ($site->status == 'invalid' || $site->status == 'sample') {
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

        // page cannot be crawled
        if (is_null($content) || strlen($content) == 0) {
            $site->statusFailHTML();
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
                    $status = false;
                    if (isset($result['error'])) {
                        if ($result['error'] == "incorrect price") {
                            $site->statusFailPrice();
                            continue;
                        } elseif ($result['error'] == "incorrect xpath") {
                            $site->statusFailXpath();
                            continue;
                        }
                    }
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

    public function crawlPage($options, CrawlerInterface $crawlerClass)
    {
        $crawlerClass->setOptions($options);
        $crawlerClass->loadHTML();
        $html = $crawlerClass->getHTML();
        return $html;
    }

    public function parserPrice($xpath, $content, ParserInterface $parserClass, CurrencyFormatterInterface $currencyFormatterClass = null)
    {
        $options = array(
            "xpath" => $xpath,
        );
        $parserClass->setOptions($options);
        $parserClass->setHTML($content);
        $parserClass->init();
        $result = $parserClass->parseHTML();
        if (!is_null($result) && (is_string($result) || is_numeric($result))) {
            $price = $result;
            $price = utf8_decode($price);
            foreach (config("constants.price_describers") as $priceDescriber) {
                $price = str_replace($priceDescriber, '', $price);
            }
            if (!is_null($currencyFormatterClass)) {
                $currencyFormatterClass->setPriceText($price);
                $currencyFormatterClass->formatPriceText();
                $price = $currencyFormatterClass->getPriceText();
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
            /*crawled content is not a price*/
            $status = false;
            $error = "incorrect xpath";
            return compact(['status', 'error']);
        }
    }
}