<?php

namespace App\Http\Controllers;

use App\Libraries\ChargifyAPI;
use Illuminate\Http\Request;

use App\Http\Requests;

class MessageController extends Controller
{
    use ChargifyAPI;

    public function welcomeSubscription()
    {
        $user = auth()->user();
        $subscription = $user->latestValidSubscription();
        $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
        return view('msg.subscription.welcome')->with(compact(['apiSubscription']));
    }
}
