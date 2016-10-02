<?php
namespace App\Contracts\Repository\Crawler;

use App\Models\Crawler;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 10:46 AM
 */
interface CrawlerContract
{
    public function getCrawlers();

    public function getCrawler($crawler_id);

    public function updateCrawler($crawler_id, $options);

    public function deleteCrawler($crawler_id);

    public function pickCrawler();

    public function setCrawlerQueuing($crawler_id);

    public function setCrawlerRunning($crawler_id);

    public function crawl(Crawler $crawler, CrawlerInterface $crawlerClass, ParserInterface $parserClass);
}