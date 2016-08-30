<?php

namespace App\Http\Controllers\Chargify;

use App\Http\Controllers\Controller;
use App\Libraries\ChargifyAPI;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;

class SubscriptionController extends Controller
{
    use ChargifyAPI;

    public function viewAPIProducts()
    {
        $chosenAPIProductIDs = array();
        $validSubscriptions = auth()->user()->validSubscriptions();
        foreach ($validSubscriptions as $subscription) {
            $chosenAPIProductIDs[] = $subscription->api_product_id;
        }


        $APIProducts = $this->getProducts();
        return view('subscriptions.subscription_plans')->with(compact(['APIProducts', 'chosenAPIProductIDs']));
    }

    public function createSubscription()
    {
        $user = auth()->user();
        /*TODO validate input here*/


        /*TODO at the moment assume that product id is always here and validate before*/
        $productId = request()->get('api_product_id');
        $product = $this->getProduct($productId);
        if ($product->require_credit_card) {
            $chargifyLink = $product->public_signup_pages[0]->url;
            $verificationCode = str_random(10);
            $user->verification_code = $verificationCode;
            $user->save();
            $reference = array(
                "user_id" => $user->getKey(),
                "verification_code" => $verificationCode
            );
            $encryptedReference = rawurlencode(json_encode($reference));
            $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}";
            return redirect()->to($chargifyLink);
        } else {

            /* create subscription in chargify */
            $fields = new \stdClass();
            $subscription = new \stdClass();
            $subscription->product_id = $product->id;
            $customer_attributes = new \stdClass();
            $customer_attributes->first_name = $user->first_name;
            $customer_attributes->last_name = $user->last_name;
            $customer_attributes->email = $user->email;
            $subscription->customer_attributes = $customer_attributes;
            $fields->subscription = $subscription;

            $result = $this->setSubscription(json_encode($fields));
            if ($result != null) {
                /* clear verification code*/
                $user->verification_code = null;
                $user->save();
                try {
                    /* update subscription record */
                    $subscription = $result->subscription;
                    $expiry_datetime = $subscription->expires_at;
                    $sub = new Subscription();
                    $sub->user_id = $user->getKey();
                    $sub->api_product_id = $subscription->product->id;
                    $sub->api_customer_id = $subscription->customer->id;
                    $sub->api_subscription_id = $subscription->id;
                    $sub->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_datetime));
                    $sub->save();
                    $title = "Welcome to SpotLite";
                    $bodyTitle = "Welcome to SpotLite";
                    $bodyContent = "[Please put the welcome message here. Chargify indicates that credit card details are correct and payment can be made through.]";
                    $bodyContent .= "<br>The available attributes in this page are subscription, product and user information in Chargify";
                    $bodyContent .= "<br>Please refer to <a href=\"https://docs.chargify.com/api-subscriptions\">https://docs.chargify.com/api-subscriptions</a>
                        for more available attributes.";

                    return view('msg.payment')->with(compact(['title', 'bodyTitle', 'bodyContent']));
                } catch (Exception $e) {
                    return $user;
                }
            }
        }
    }
}
