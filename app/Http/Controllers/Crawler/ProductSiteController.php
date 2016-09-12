<?php
namespace App\Http\Controllers\Crawler;

use App\Http\Controllers\Controller;
use Invigor\Crawler\Contracts\CrawlerInterface;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/12/2016
 * Time: 5:29 PM
 */
class ProductSiteController extends Controller
{
    public function __construct()
    {
        app()->bind('Invigor\Crawler\Contracts\CrawlerInterface', 'Invigor\Crawler\Repositories\DefaultCrawler');
    }

    public function index(CrawlerInterface $crawler)
    {
        $options = array(
            "url" => "https://www.bigw.com.au/product/smart-value-mega-towel/p/WCC100000000300062/"
        );

        $crawler->setOptions($options);
        $crawler->loadHTML();
        return ($crawler->getHTML());
    }
}