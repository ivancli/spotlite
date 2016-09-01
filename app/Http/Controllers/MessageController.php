<?php

namespace App\Http\Controllers;

use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Models\Subscription;
use Illuminate\Http\Request;

use App\Http\Requests;

class MessageController extends Controller
{
    protected $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    public function welcomeSubscription($raw = 0)
    {
        $user = auth()->user();
        $subscription = $user->latestValidSubscription();
        $apiSubscription = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);
        if ($raw == 0) {
            return view('msg.subscription.welcome')->with(compact(['apiSubscription']));
        } else {
            return view('msg.subscription.raw.welcome')->with(compact(['apiSubscription']));
        }
    }

    public function updateSubscription($raw = 0)
    {
        $user = auth()->user();
        $subscription = $user->latestValidSubscription();
        $apiSubscription = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);
        if ($raw == 0) {
            return view('msg.subscription.welcome')->with(compact(['apiSubscription']));
        } else {
            return view('msg.subscription.raw.welcome')->with(compact(['apiSubscription']));
        }
    }

    public function cancelledSubscription($subscription_id, $raw = 0)
    {
        $user = auth()->user();
        $subscription = Subscription::findOrFail($subscription_id);
        if ($subscription->user_id == $user->getKey()) {
            $apiSubscription = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);
            if ($raw == 0) {
                return view('msg.subscription.cancelled')->with(compact(['apiSubscription']));
            } else {
                return view('msg.subscription.raw.cancelled')->with(compact(['apiSubscription']));
            }
        } else {
            abort(403);
            return false;
        }
    }
}
