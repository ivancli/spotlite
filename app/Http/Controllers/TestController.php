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
use App\Models\SitePreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    }
}