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

class APISubscriptionController extends Controller
{
    use ChargifyAPI;

    /**
     * Called when user register but not yet selected a package
     * @return $this
     */
    public function viewAPIProducts()
    {
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
        return view('subscriptions.subscription_plans')->with(compact(['products', 'chosenAPIProductIDs']));
    }

    public function createSubscription()
    {
        $user = auth()->user();
        if (!request()->has('api_product_id')) {
            /* TODO should handle the error in a better way*/
            abort(403);
            return false;
        }

        $productId = request()->get('api_product_id');
        $product = $this->getProduct($productId);
        if (!is_null($product)) {
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
                        $user->verification_code = null;
                        $user->save();

                        $subscription_id = $request->get('id');
                        $subscription = $this->getSubscription($subscription_id);
                        if ($user->latestValidSubscription() != false) {
//                            foreach ($user->subscriptions as $userSub) {
                            $sub = $user->latestValidSubscription();
                            $expiry_datetime = $subscription->expires_at;
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($expiry_datetime) ? null : date('Y-m-d H:i:s', strtotime($expiry_datetime));
                            $sub->save();

                            return redirect()->route('msg.subscription.update');
//                            }
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

    public function updateSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
        /*TODO check current subscription has payment method or not*/
        if (is_null($apiSubscription->payment_type)) {
            $this->createSubscription();
        } else {
            $fields = new \stdClass();
            $migration = new \stdClass();
            $migration->product_id = request()->get('api_product_id');
            $fields->migration = $migration;

//        $result = $this->previewMigration($apiSubscription->id, json_encode($fields));
            $result = $this->migrateSubscription($apiSubscription->id, json_encode($fields));
            if ($result != false) {
                if (!is_null($result->subscription)) {
                    $subscription->api_product_id = $result->subscription->product->id;
                    if (!is_null($result->subscription->canceled_at)) {
                        $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->subscription->canceled_at));
                    }
                    if (!is_null($result->subscription->expires_at)) {
                        $subscription->expiry_date = date('Y-m-d H:i:s', strtotime($result->subscription->expires_at));
                    }
                    $subscription->save();
                    return redirect()->route('msg.subscription.update');
                }
            }
        }
    }

    public function cancelSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
        if (!is_null($apiSubscription) && is_null($apiSubscription->canceled_at)) {
            $result = $this->cancelSubscriptionBySubscriptionID($apiSubscription->id);
            if (!is_null($result->subscription->canceled_at)) {
                $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->subscription->canceled_at));
                $subscription->save();
                return redirect()->route('msg.subscription.cancelled', $subscription->getkey());
            } else {
                abort(500);
                return false;
            }
        } else {
            /*TODO enhance error handling*/
            abort(404);
            return false;
        }
    }
}
