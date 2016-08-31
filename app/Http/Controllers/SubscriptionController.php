<?php

namespace App\Http\Controllers;

use App\Libraries\ChargifyAPI;
use App\Models\Subscription;
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
        $sub = $user->latestValidSubscription();
        $current_sub_id = $user->latestValidSubscription()->api_subscription_id;
        $subscription = $this->getSubscription($current_sub_id);

        return view('subscriptions.index')->with(compact(['sub', 'allSubs', 'subscription']));
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function edit($id)
    {
        $subscription = auth()->user()->latestValidSubscription();
        /*TODO validate the $subscription*/

        $chosenAPIProductIDs = array();
        $validSubscriptions = auth()->user()->validSubscriptions();
        foreach ($validSubscriptions as $subscription) {
            $chosenAPIProductIDs[] = $subscription->api_product_id;
        }

        //load all products from Chargify
        $products = $this->getProducts();
        foreach ($products as $index => $product) {
            if (auth()->user()->subscriptions->count() != 0 && $product->product->price_in_cents == 0) {
                unset($products[$index]);
            }
        }
        return view('subscriptions.edit')->with(compact(['products', 'chosenAPIProductIDs', 'subscription']));
    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
