<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use App\Contracts\Repository\Ebay\EbayContract;
use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Jobs\CrawlSite;
use App\Models\Crawler;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Invigor\Crawler\Repositories\Crawlers;
use Invigor\Crawler\Repositories\Crawlers\DefaultCrawler;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    var $request;
    var $mailingAgentRepo;

    public function __construct(Request $request, MailingAgentContract $mailingAgentContract)
    {
        $this->request = $request;
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    public function index()
    {
        $this->mailingAgentRepo->addSubscriber(array(
            'EmailAddress' => 'ivan.li_live_au_2',
            'Name' => 'invigor' . " " . 'test',
        ));

    }
}