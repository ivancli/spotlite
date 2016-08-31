<?php

namespace App\Http\Controllers;

use App\Libraries\ChargifyAPI;
use App\Models\Subscription;
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

    public function updateSubscription()
    {
        $user = auth()->user();
        $subscription = $user->latestValidSubscription();
        $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
        return view('msg.subscription.welcome')->with(compact(['apiSubscription']));
    }

    public function cancelledSubscription($subscription_id)
    {
        $user = auth()->user();
        $subscription = Subscription::findOrFail($subscription_id);
        if($subscription->user_id == $user->getKey())
        {
            $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
            return view('msg.subscription.cancelled')->with(compact(['apiSubscription']));
        }else{
            abort(403);
            return false;
        }
    }
}
