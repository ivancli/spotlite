<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Contracts\Repository\User\User\UserContract;
use App\Models\Subscription;
use Illuminate\Http\Request;

use App\Http\Requests;
use Invigor\Chargify\Chargify;

class MessageController extends Controller
{
    protected $subscriptionRepo;
    protected $userRepo;

    public function __construct(SubscriptionContract $subscriptionContract, UserContract $userContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
        $this->userRepo = $userContract;
    }

    public function welcomeSubscription($raw = 0)
    {
        $user = auth()->user();
        if ($user->needSubscription) {
            $subscription = $user->subscription;
            $apiSubscription = Chargify::subscription($user->subscription_location)->get($subscription->api_subscription_id);
        }
        $sampleUser = $this->userRepo->sampleUser();

        //sample data order by category names ascending
        $sampleData = $sampleUser->categories()->orderBy('category_name', 'asc')->get()->pluck(['category_name'])->all();

        if ($raw == 0) {
            return view('msg.subscription.welcome')->with(compact(['apiSubscription', 'sampleData']));
        } else {
            return view('msg.subscription.raw.welcome')->with(compact(['apiSubscription', 'sampleData']));
        }
    }

    public function updateSubscription($raw = 0)
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $apiSubscription = Chargify::subscription($user->subscription_location)->get($subscription->api_subscription_id);
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
            $apiSubscription = Chargify::subscription($user->subscription_location)->get($subscription->api_subscription_id);
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
        $user = auth()->user();
        $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($user, $apiSubscriptionId);

        if ($raw == 0) {
            return view('msg.subscription.credit_card_expiry')->with(compact(['updatePaymentLink']));
        } else {
            /*TODO implement this if needed*/
        }
    }
}
