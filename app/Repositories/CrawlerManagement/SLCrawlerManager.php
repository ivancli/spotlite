<?php
namespace App\Repositories\CrawlerManagement;

use App\Contracts\CrawlerManagement\CrawlerManager;
use App\Models\Crawler;
use App\Models\Domain;
use App\Models\HistoricalPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 10:48 AM
 */
class SLCrawlerManager implements CrawlerManager
{
    public function getCrawlers()
    {
        // TODO: Implement getCrawlers() method.
    }

    public function getCrawler($crawler_id)
    {
        $crawler = Crawler::findOrFail($crawler_id);
        return $crawler;
    }

    public function updateCrawler($crawler_id, $options)
    {
        // TODO: Implement updateCrawler() method.
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
        $crawler->status = "running";
        $crawler->save();

        $site = $crawler->site;
        $options = array(
            "url" => $site->site_url,
        );
        $crawlerClass->setOptions($options);
        $crawlerClass->loadHTML();
        $html = $crawlerClass->getHTML();


        if (is_null($html) || strlen($html) == 0) {
            /*TODO handle error, page not crawled*/
            file_put_contents('/home/vagrant/Code/spotlite/storage/logs/ivan.log', "unable to crawl the web page");
        }

        $xpath = $site->site_xpath;
        if (is_null($xpath)) {
            $domain = Domain::where('domain_url', $site->domain)->first();
            if (!is_null($domain)) {
                $xpath = $domain->domain_xpath;
            }
        }
        if ($xpath != null) {
            $options = array(
                "xpath" => $xpath,
            );
            $parserClass->setOptions($options);
            $parserClass->setHTML($html);
            $parserClass->init();
            $result = $parserClass->parseHTML();
            if (!is_null($result) && is_string($result)) {
                $price = str_replace('$', '', $result);
                $price = floatval($price);
                if ($price > 0) {
                    /*TODO now you got the $price*/

                    $historicalPrice = HistoricalPrice::create(array(
                        "crawler_id" => $crawler->getKey(),
                        "site_id" => $site->getKey(),
                        "price" => $price
                    ));

                    $site->recent_price = $price;
                    $site->last_crawled_at = $historicalPrice->created_at;
                    $site->save();
                    $site->statusOK();
                } else {
                    /*TODO handle error, price is incorrect*/
                    $site->statusFailPrice();
                    file_put_contents('/home/vagrant/Code/spotlite/storage/logs/ivan.log', "price is incorrect");
                }
            } else {
                /*TODO handle error, xpath is incorrect*/
                $site->statusFailXpath();
                file_put_contents('/home/vagrant/Code/spotlite/storage/logs/ivan.log', "xpath is incorrect");
            }
        } else {
            /*TODO handle error, cannot find xpath*/
            $site->statusNullXpath();
            file_put_contents('/home/vagrant/Code/spotlite/storage/logs/ivan.log', "cannot find xpath");
        }
        $crawler->resetStatus();
    }
}