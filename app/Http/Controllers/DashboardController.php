<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers;


use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Models\AppPreference;
use Invigor\Crawler\Repositories\DefaultCrawler;

class DashboardController extends Controller
{
    protected $subscriptionManager;
    protected $emailGenerator;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    public function index()
    {
        $subscriptions = request()->user()->subscriptions;
        foreach ($subscriptions as $subscription) {
            $apiSub = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);
        }

        return view('dashboard.index');
    }
}