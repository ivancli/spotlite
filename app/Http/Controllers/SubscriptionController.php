<?php

namespace App\Http\Controllers;

use App\Libraries\ChargifyAPI;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    use ChargifyAPI;

    public function index()
    {
        $user = auth()->user();
        $allSubs = $user->subscriptions;
        $current_sub_id = $user->latestValidSubscription()->api_subscription_id;
        $subscription = $this->getSubscription($current_sub_id);

        return view('subscriptions.index')->with(compact(['allSubs', 'subscription']));
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
