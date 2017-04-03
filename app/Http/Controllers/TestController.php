<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $reportTask = auth()->user()->reportTask;
        $user = $reportTask->reportable;

        $sites = $user->sites;

        $siteChangeCounter = 0;
        $cheapestCounter = 0;
        $mostExpensiveCounter = 0;
        $failedCrawlerCounter = 0;
        $showLastChange = false;
        foreach ($sites as $site) {
            switch ($reportTask->frequency) {
                case 'daily':
                    if (!is_null($site->priceLastChangedAt) && $site->priceLastChangedAt->diffInHours(Carbon::now()) < 24) {
                        $siteChangeCounter++;
                        $showLastChange = true;
                    }
                    break;
                case 'weekly':
                    if (!is_null($site->priceLastChangedAt) && $site->priceLastChangedAt->diffInHours(Carbon::now()) < 168) {
                        $siteChangeCounter++;
                        $showLastChange = true;
                    }
                    break;
            }
            if ($site->my_price == 'y' && $site->isCheapest) {
                $cheapestCounter++;
            }
            if ($site->my_price == 'y' && $site->isMostExpensive) {
                $mostExpensiveCounter++;
            }
            if($site->status != 'ok' && $site->status != 'waiting'){
                $failedCrawlerCounter++;
            }
        }

        $view = 'products.report.email.user';
        return view($view)->with(compact(['reportTask', 'siteChangeCounter', 'cheapestCounter', 'mostExpensiveCounter', 'failedCrawlerCounter', 'showLastChange']));
    }
}