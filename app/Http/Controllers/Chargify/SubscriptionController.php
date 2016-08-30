<?php

namespace App\Http\Controllers\Chargify;

use App\Http\Controllers\Controller;
use App\Libraries\ChargifyAPI;
use App\Models\Subscription;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        //load all products from Chargify
        $APIProducts = $this->getProducts();

        return view('subscriptions.subscription_plans')->with(compact(['APIProducts', 'chosenAPIProductIDs']));
    }

    public function createSubscription()
    {
        $user = auth()->user();
        if(!request()->has('api_product_id')){
            /* TODO should handle the error in a better way*/
            abort(403);
            return false;
        }

        $productId = request()->get('api_product_id');
        $product = $this->getProduct($productId);
        if(!is_null($product)){
            if ($product->require_credit_card) {
                /* redirect to Chargify payment gateway (signup page) */
                $chargifyLink = array_first($product->public_signup_pages)->url;
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
                /* create subscription in Chargify by using its API */
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
                        return redirect()->route('msg.subscription.welcome');
                    } catch (Exception $e) {
                        return $user;
                    }
                }
            }
        }
    }


    public function finishPayment(Request $request)
    {
        if (!$request->has('ref') || !$request->has('id')) {
            abort(403, "unauthorised access");
        } else {

            $reference = $request->get('ref');
            $reference = json_decode($reference);
            try {
                if (property_exists($reference, 'user_id') && property_exists($reference, 'verification_code')) {
                    $user = User::findOrFail($reference->user_id);
                    if ($user->verification_code == $reference->verification_code) {
                        /* todo enable this once it's live */
                        $user->verification_code = null;
                        $user->save();

                        /*todo UPDATE AND CHECK SUBSCRIPTION STATUS*/
                        $subscription_id = $request->get('id');
                        $subscription = $this->getSubscription($subscription_id);
                        if ($user->subscriptions->count() > 0) {
                            foreach ($user->subscriptions as $userSub) {
                                /*todo find out the not expired one*/


                            }
                        } else {
                            /* create subscription record in DB */
                            $expiry_datetime = $subscription->expires_at;
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($expiry_datetime) ? null : date('Y-m-d H:i:s', strtotime($expiry_datetime));
                            $sub->save();

                            return redirect()->route('msg.subscription.welcome');
//                            return redirect()->route('dashboard.index');
                        }
                    } else {
                        abort(403, "unauthorised access");
                        return false;
                    }
                } else {
                    abort(404, "page not found");
                    return false;
                }

            } catch (ModelNotFoundException $e) {
                abort(404, "page not found");
                return false;
            }

        }
    }
}
