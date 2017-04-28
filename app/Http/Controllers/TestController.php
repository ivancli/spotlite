<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use App\Contracts\Repository\Ebay\EbayContract;
use App\Jobs\CrawlSite;
use App\Models\Crawler;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Invigor\Crawler\Repositories\Crawlers;
use Invigor\Crawler\Repositories\Crawlers\DefaultCrawler;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    var $request;
    var $ebayRepo;

    public function __construct(Request $request, EbayContract $ebayContract)
    {
        $this->request = $request;
        $this->ebayRepo = $ebayContract;
    }

    public function index()
    {
        $defaultCrawler = app(DefaultCrawler::class);

        $options = array(
            "url" => "http://musiciansoasis.com.au/ibanez/467-ibanez-m80m-8-string-meshuggah-signature-electric-guitar.html",
        );
        $defaultCrawler->setOptions($options);
        $defaultCrawler->loadHTML();
        $html = $defaultCrawler->getHTML();
        dd($html);
    }
}