<?php

namespace App\Http\Controllers;

use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Events\Subscription\SubscriptionCancelled;
use App\Events\Subscription\SubscriptionCancelling;
use App\Events\Subscription\SubscriptionCompleted;
use App\Events\Subscription\SubscriptionCreating;
use App\Events\Subscription\SubscriptionEditViewed;
use App\Events\Subscription\SubscriptionManagementViewed;
use App\Events\Subscription\SubscriptionUpdated;
use App\Events\Subscription\SubscriptionUpdating;
use App\Events\Subscription\SubscriptionViewed;
use App\Models\Subscription;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    protected $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
        /*TODO need to handle middleware for each function*/
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewProducts()
    {
        $subscription = auth()->user()->validSubscription();
        if (!is_null($subscription)) {
            $chosenAPIProductID = $subscription->api_product_id;
        }

        /*load all products/services*/
        $products = $this->subscriptionManager->getProducts();

        /* remove the trail/free product for the existing subscriber */
        foreach ($products as $index => $product) {
            if (!is_null($subscription) && $product->product->price_in_cents == 0) {
                unset($products[$index]);
            }
        }
        event(new SubscriptionViewed());
        return view('subscriptions.subscription_plans')->with(compact(['products', 'chosenAPIProductID']));
    }

    /**
     * Manage My Subscription - page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $sub = $user->validSubscription();
        if (!is_null($sub)) {
            $this->subscriptionManager->updateCreditCardDetails($sub);
            $current_sub_id = $sub->api_subscription_id;
            $subscription = $this->subscriptionManager->getSubscription($current_sub_id);

            $portalEnabled = !is_null($subscription->customer->portal_customer_created_at);
            if ($portalEnabled) {
                $portalLink = $this->subscriptionManager->getBillingPortalLink($sub);
            }

            $updatePaymentLink = $this->subscriptionManager->generateUpdatePaymentLink($current_sub_id);
            event(new SubscriptionManagementViewed());
            return view('subscriptions.index')->with(compact(['sub', 'allSubs', 'subscription', 'updatePaymentLink', 'portalLink']));
        }
    }

    public function store(Request $request)
    {
        event(new SubscriptionCreating());
        $user = auth()->user();
        if (!request()->has('api_product_id')) {
            /* TODO should handle the error in a better way*/
            abort(403);
            return false;
        }
        $productId = request()->get('api_product_id');
        $couponCode = $request->get('coupon_code');
        $product = $this->subscriptionManager->getProduct($productId);
        if (!is_null($product)) {
            if ($product->require_credit_card) {
                if (!is_null(auth()->user()->subscription)) {
                    $previousSubscription = auth()->user()->subscription;
                    $previousAPISubscription = $this->subscriptionManager->getSubscription($previousSubscription->api_subscription_id);
                    if(isset($previousAPISubscription->credit_card)){
                        $previousAPICreditCard = $previousAPISubscription->credit_card;
                        if (!is_null($previousAPICreditCard)) {
                            if ($previousAPICreditCard->expiration_year > date("Y") || ($previousAPICreditCard->expiration_year == date("Y") && $previousAPICreditCard->expiration_month >= date('n'))) {
                                $fields = new \stdClass();
                                $subscription = new \stdClass();
                                $subscription->product_id = $product->id;
                                $subscription->customer_id = $previousSubscription->api_customer_id;
                                $subscription->payment_profile_id = $previousAPICreditCard->id;
                                $subscription->coupon_code = $couponCode;
                                $fields->subscription = $subscription;
                                $result = $this->subscriptionManager->storeSubscription(json_encode($fields));
                                if (isset($result->subscription)) {
//                                $subscription = new Subscription();
//                                $subscription->user_id = auth()->user()->getKey();
                                    $previousSubscription->api_product_id = $result->subscription->product->id;
                                    $previousSubscription->api_subscription_id = $result->subscription->id;
                                    $previousSubscription->api_customer_id = $result->subscription->customer->id;
                                    $previousSubscription->cancelled_at = null;
                                    $previousSubscription->save();
                                    return redirect()->route('subscription.index');
                                }
                            }
                        }
                    }
                }
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
                $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}&coupon_code={$couponCode}";
                Cache::tags(["user_subscription_". $user->getKey()])->flush();
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

//                $result = $this->setSubscription(json_encode($fields));
                $result = $this->subscriptionManager->storeSubscription(json_encode($fields));
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
                        event(new SubscriptionCompleted($sub));
                        Cache::tags(["user_subscription_". $user->getKey()])->flush();
                        return redirect()->route('subscription.index');
                    } catch (Exception $e) {
                        /*TODO need to handle exception properly*/
                        return $user;
                    }
                }
            }
        }
    }

    public function finalise(Request $request)
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
                        /*TODO fucking enable this*/
//                        $user->save();

                        $subscription_id = $request->get('id');
                        $subscription = $this->subscriptionManager->getSubscription($subscription_id);
                        if (!is_null($user->subscription)) {
                            $sub = $user->subscription;
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();
                            $this->subscriptionManager->updateCreditCardDetails($sub);
                            event(new SubscriptionUpdated($sub));
                            Cache::tags(["user_subscription_". $user->getKey()])->flush();
                            return redirect()->route('subscription.index');
//                            }
                        } else {
                            /* create subscription record in DB */
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();
                            $this->subscriptionManager->updateCreditCardDetails($sub);
                            event(new SubscriptionCompleted($sub));
                            Cache::tags(["user_subscription_". $user->getKey()])->flush();
                            return redirect()->route('subscription.index');
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

    public function externalUpdate(Request $request)
    {
//        dd($request->server('HTTP_REFERER'));
        /*TODO validation here*/
        $ref = json_decode($request->get('ref'));
        $user_id = $ref->user_id;

        if (auth()->user()->getKey() != $user_id) {
            abort(403);
        }
        $this->subscriptionManager->syncUserSubscription(auth()->user());
        Cache::tags(["user_subscription_". $user_id])->flush();
        return redirect()->route('subscription.index');
    }

    public function edit($id)
    {

        $subscription = auth()->user()->validSubscription();
        /*TODO validate the $subscription*/

        $chosenAPIProductID = $subscription->api_product_id;

        //load all products from Chargify
//        $products = $this->getProducts();
        $products = $this->subscriptionManager->getProducts();

        /* remove the trail/free product for the existing subscriber */
        foreach ($products as $index => $product) {
            if (!is_null(auth()->user()->subscription) && $product->product->price_in_cents == 0) {
                unset($products[$index]);
            }
        }
        event(new SubscriptionEditViewed($subscription));
        return view('subscriptions.edit')->with(compact(['products', 'chosenAPIProductID', 'subscription']));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        event(new SubscriptionUpdating($subscription));
        $apiSubscription = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);

        if ($request->has('coupon_code')) {
            $fields = new \stdClass();
            $updatedSubscription = new \stdClass();
            $updatedSubscription->coupon_code = $request->get('coupon_code');
            $fields->subscription = $updatedSubscription;
            $result = $this->subscriptionManager->updateSubscription($apiSubscription->id, json_encode($fields));
            if ($result == false) {
                if ($request->ajax()) {
                    $status = false;
                    if ($request->wantsJson()) {
                        return response()->json(compact(['status']));
                    } else {
                        return compact(['status']);
                    }
                } else {
                    return redirect()->back();
                }
            }
        }
        $coupon_code = $request->get('coupon_code');
        /*check current subscription has payment method or not*/
        if (is_null($apiSubscription->payment_type)) {
            //current subscription no payment method
            return $this->store($request);
        } else {
            //current subscription has payment method
            $fields = new \stdClass();
            $migration = new \stdClass();
            $migration->product_id = request()->get('api_product_id');
            $migration->include_coupons = 1;
            $fields->migration = $migration;

            $result = $this->subscriptionManager->setMigration($apiSubscription->id, json_encode($fields));
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
                    event(new SubscriptionUpdated($subscription));
                    Cache::tags(["user_subscription_". $subscription->user_id])->flush();
                    if ($request->ajax()) {
                        $status = true;
                        if ($request->wantsJson()) {
                            return response()->json(compact(['status', 'subscription']));
                        } else {
                            return compact(['status', 'subscription']);
                        }
                    } else {
                        return redirect()->route('msg.subscription.update');
                    }
                }
            }
        }
    }

    /**
     * Cancel subscription
     * @param $id
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        event(new SubscriptionCancelling($subscription));
        $apiSubscription = $this->subscriptionManager->getSubscription($subscription->api_subscription_id);
        if (!is_null($apiSubscription) && is_null($apiSubscription->canceled_at)) {
            $result = $this->subscriptionManager->cancelSubscription($apiSubscription->id);
            if (!is_null($result->subscription->canceled_at)) {
                $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->subscription->canceled_at));
                $subscription->save();
                event(new SubscriptionCancelled($subscription));
                Cache::tags(["user_subscription_". $subscription->user_id])->flush();
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
