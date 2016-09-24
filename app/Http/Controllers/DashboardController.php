<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers;


use App\Contracts\SubscriptionManagement\SubscriptionManager;

class DashboardController extends Controller
{
    protected $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    public function index()
    {
        return view('dashboard.index');
    }
}