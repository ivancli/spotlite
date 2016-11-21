<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Models\Subscription;
use Illuminate\Http\Request;

use App\Http\Requests;
use Invigor\Chargify\Chargify;

class MessageController extends Controller
{
    protected $subscriptionRepo;

    public function __construct(SubscriptionContract $subscriptionContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
    }

    public function welcomeSubscription($raw = 0)
    {
        $user = auth()->user();
        if (!auth()->user()->isStaff) {
            $subscription = $user->subscription;
            $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
        }
        if ($raw == 0) {
            return view('msg.subscription.welcome')->with(compact(['apiSubscription']));
        } else {
            return view('msg.subscription.raw.welcome')->with(compact(['apiSubscription']));
        }
    }

    public function updateSubscription($raw = 0)
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
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
            $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
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

    public function notifyCreditCardExpiringSoon($raw = 0)
    {
        $apiSubscriptionId = auth()->user()->subscription->api_subscription_id;
        $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($apiSubscriptionId);

        if ($raw == 0) {
            return view('msg.subscription.credit_card_expiry')->with(compact(['updatePaymentLink']));
        } else {
            /*TODO implement this if needed*/
        }
    }
}
