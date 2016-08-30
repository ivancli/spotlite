<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers;


use App\Libraries\ChargifyAPI;

class DashboardController extends Controller
{
    use ChargifyAPI;
    public function index()
    {
        dump(request()->user()->hasValidSubscription());
        $subscriptions = request()->user()->subscriptions;
        foreach($subscriptions as $subscription){
            $apiSub = $this->getSubscription($subscription->api_subscription_id);
            dump($apiSub);
        }
        return view('dashboard.index');
    }
}