<?php

namespace App\Http\Controllers\Subscription;

use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Events\Subscription\SubscriptionCancelled;
use App\Events\Subscription\SubscriptionCancelling;
use App\Events\Subscription\SubscriptionCompleted;
use App\Events\Subscription\SubscriptionCreating;
use App\Events\Subscription\SubscriptionEditViewed;
use App\Events\Subscription\SubscriptionManagementViewed;
use App\Events\Subscription\SubscriptionUpdated;
use App\Events\Subscription\SubscriptionUpdating;
use App\Events\Subscription\SubscriptionViewed;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Chargify;

class SubscriptionController extends Controller
{
    protected $subscriptionRepo;

    public function __construct(SubscriptionContract $subscriptionContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
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
        $productFamilies = $this->subscriptionRepo->getProductList();
        event(new SubscriptionViewed());
        return view('subscriptions.subscription_plans')->with(compact(['productFamilies', 'chosenAPIProductID']));
    }

    /**
     * Manage My Subscription - page
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $sub = $user->validSubscription();
        if (!is_null($sub)) {
            $current_sub_id = $sub->api_subscription_id;
            $subscription = $user->cachedAPISubscription();
            if ($subscription != false) {
                $portalLink = Chargify::customer()->getLink($subscription->customer_id);
                $transactions = Chargify::transaction()->allBySubscription($current_sub_id);
                $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($current_sub_id);

                $components = Chargify::component()->allBySubscription($current_sub_id);
                if (!isset($components->errors) && count($components) > 0) {
                    $component = array_first($components);
                } else {
                    abort(403);
                    return false;
                }

                event(new SubscriptionManagementViewed());
                return view('subscriptions.index')->with(compact(['sub', 'allSubs', 'subscription', 'updatePaymentLink', 'portalLink', 'transactions', 'component']));
            } else {
                abort(403);
                return false;
            }
        } else {

        }
    }

    public function store(Request $request)
    {
        event(new SubscriptionCreating());
        $user = auth()->user();
        if (!$request->has('api_product_id')) {
            /* TODO should handle the error in a better way*/
            abort(403);
            return false;
        }
        $productId = $request->get('api_product_id');
        $couponCode = $request->get('coupon_code');
        $product = Chargify::product()->get($productId);
        $product_family_id = $product->product_family_id;

        $components = Chargify::component()->allByProductFamily($product_family_id);
        if (!isset($components->errors) && count($components) > 0) {
            $component = array_first($components);

            if (!isset($product->errors)) {
                if ($product->require_credit_card) {
                    if (!is_null(auth()->user()->subscription)) {
                        $previousSubscription = auth()->user()->subscription;
                        $previousAPISubscription = Chargify::subscription()->get($previousSubscription->api_subscription_id);
                        $paymentProfile = $previousAPISubscription->paymentProfile();
                        if (!isset($paymentProfile->errors) && !is_null($paymentProfile)) {
                            if ($paymentProfile->expiration_year > date("Y") || ($paymentProfile->expiration_year == date("Y") && $paymentProfile->expiration_month >= date('n'))) {
                                $newSubscription = Chargify::subscription()->create(array(
                                    "product_id" => $product->id,
                                    "customer_id" => $previousSubscription->api_customer_id,
                                    "payment_profile_id" => $paymentProfile->id,
                                    "coupon_code" => $couponCode,
                                ));
                                $this->_flushUserSubscriptionCache($user->getKey());
                                if (!isset($newSubscription->errors)) {

                                    $newSubscription = Chargify::subscription()->update($newSubscription->id, array(
                                        "components" => array(
                                            array(
                                                "component" => array(
                                                    "component_id" => $component->id
                                                )
                                            )
                                        )
                                    ));

                                    Chargify::allocation()->create($newSubscription->id, $component->id, array(
                                        "quantity" => array_first($component->prices)->ending_quantity,
                                    ));

                                    $previousSubscription->api_product_id = $newSubscription->product_id;
                                    $previousSubscription->api_subscription_id = $newSubscription->id;
                                    $previousSubscription->api_customer_id = $newSubscription->customer_id;
                                    $previousSubscription->cancelled_at = null;
                                    $previousSubscription->save();
                                    return redirect()->route('subscription.index');
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

                    $component_quantity = is_null(array_first($component->prices)->ending_quantity) ? 0 : array_first($component->prices)->ending_quantity;
                    $encryptedReference = rawurlencode(json_encode($reference));
                    $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}&coupon_code={$couponCode}&components[][component_id]={$component->id}&components[][allocated_quantity]={$component_quantity}";
                    $this->_flushUserSubscriptionCache($user->getKey());
                    return redirect()->to($chargifyLink);
                } else {
                    $newSubscription = Chargify::subscription()->create(array(
                        "product_id" => $product->id,
                        "customer_attributes" => array(
                            "first_name" => $user->first_name,
                            "last_name" => $user->last_name,
                            "email" => $user->email
                        )
                    ));

                    if (!isset($newSubscription->errors)) {
                        /* clear verification code*/
                        $user->verification_code = null;
                        $user->save();
                        try {
                            /* update subscription record */
                            $expiry_datetime = $newSubscription->expires_at;
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $newSubscription->product_id;
                            $sub->api_customer_id = $newSubscription->customer_id;
                            $sub->api_subscription_id = $newSubscription->id;
                            $sub->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_datetime));
                            $sub->save();
                            event(new SubscriptionCompleted($sub));
                            $this->_flushUserSubscriptionCache($user->getKey());
                            return redirect()->route('subscription.index');
                        } catch (Exception $e) {
                            /*TODO need to handle exception properly*/
                            return $user;
                        }
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
//                        $user->save();

                        $subscription_id = $request->get('id');
                        $subscription = Chargify::subscription()->get($subscription_id);
                        if (!is_null($user->subscription)) {
                            $sub = $user->subscription;
                            $sub->api_product_id = $subscription->product_id;
                            $sub->api_customer_id = $subscription->customer_id;
                            $sub->api_subscription_id = $subscription->id;
                            $components = Chargify::component()->allBySubscription($subscription_id);
                            if (!isset($components->errors) && count($components) > 0) {
                                $component = array_first($components);
                            } else {
                                abort(403);
                                return false;
                            }
                            $sub->api_component_id = $component->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();

                            $this->subscriptionRepo->updateCreditCardDetails($sub);
                            event(new SubscriptionUpdated($sub));
                            $this->_flushUserSubscriptionCache($user->getKey());
                            return redirect()->route('subscription.index');
//                            }
                        } else {
                            /* create subscription record in DB */
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $subscription->product_id;
                            $sub->api_customer_id = $subscription->customer_id;
                            $sub->api_subscription_id = $subscription->id;
                            $components = Chargify::component()->allBySubscription($subscription->id);
                            if (!isset($components->errors) && count($components) > 0) {
                                $component = array_first($components);
                            } else {
                                abort(403);
                                return false;
                            }
                            $sub->api_component_id = $component->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();
                            $this->subscriptionRepo->updateCreditCardDetails($sub);
                            event(new SubscriptionCompleted($sub));
                            $this->_flushUserSubscriptionCache($user->getKey());
                            return redirect()->route('subscription.index');
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
        $this->subscriptionRepo->syncUserSubscription(auth()->user());
        $this->_flushUserSubscriptionCache($user_id);
        return redirect()->route('subscription.index');
    }

    public function edit($id)
    {

        $subscription = auth()->user()->validSubscription();
        /*TODO validate the $subscription*/

        $chosenAPIProductID = $subscription->api_product_id;

        //load all products from Chargify
        $productFamilies = $this->subscriptionRepo->getProductList();
        event(new SubscriptionEditViewed($subscription));
        return view('subscriptions.edit')->with(compact(['productFamilies', 'chosenAPIProductID', 'subscription']));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        if (auth()->user()->getKey() != $subscription->user_id) {
            abort(403);
            return false;
        }

        event(new SubscriptionUpdating($subscription));
        $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
//        $apiSubscription = $this->subscriptionRepo->getSubscription($subscription->api_subscription_id);

        if ($request->has('coupon_code')) {
//            $result = $this->subscriptionRepo->addCouponCode($apiSubscription->id, $request->get('coupon_code'));
            $result = Chargify::subscription()->addCoupon($apiSubscription->id, $request->get('coupon_code'));
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
            $fields = array(
                "product_id" => request()->get('api_product_id'),
                "include_coupons" => 1
            );
            $result = Chargify::subscription()->createMigration($apiSubscription->id, $fields);
//            $result = $this->subscriptionRepo->setMigration($apiSubscription->id, json_encode($fields));
            if (!isset($result->errors)) {
                $product_family_id = $result->product()->product_family_id;

                $apiComponents = Chargify::component()->allByProductFamily($product_family_id);

                $component = array_first($apiComponents);

                $apiSubscription = Chargify::subscription()->update($apiSubscription->id, array(
                    "components" => array(
                        array(
                            "component" => array(
                                "component_id" => $component->id
                            )
                        )
                    )
                ));
//                $this->subscriptionRepo->setComponentBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);
                Chargify::allocation()->create($apiSubscription->id, $component->id, array(
                    "quantity" => $component->prices[0]->ending_quantity
                ));

//                $this->subscriptionRepo->setComponentAllocationBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);
                $subscription->api_product_id = $apiSubscription->product_id;
                if (!is_null($result->canceled_at)) {
                    $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->canceled_at));
                }
                if (!is_null($result->expires_at)) {
                    $subscription->expiry_date = date('Y-m-d H:i:s', strtotime($result->expires_at));
                }
                $subscription->save();
                event(new SubscriptionUpdated($subscription));
                $this->_flushUserSubscriptionCache($subscription->user_id);
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

    /**
     * Cancel subscription
     * @param Request $request
     * @param $id
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        if (auth()->user()->getKey() != $subscription->user_id) {
            abort(403);
            return false;
        }

        event(new SubscriptionCancelling($subscription));
        $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
        if (!isset($apiSubscription->errors)) {
            if (!$request->has('keep_profile') || $request->get('keep_profile') != '1') {
                Chargify::subscription()->deletePaymentProfile($apiSubscription->id, $apiSubscription->credit_card_id);
                Chargify::subscription()->deletePaymentProfile($apiSubscription->id, $apiSubscription->bank_account_id);
            }
            Chargify::subscription()->cancel($apiSubscription->id);
            $updatedSubscription = Chargify::subscription()->get($apiSubscription->id);
            if (!isset($updatedSubscription->errors)) {
                $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($updatedSubscription->canceled_at));
                $subscription->save();
                event(new SubscriptionCancelled($subscription));
                $this->_flushUserSubscriptionCache($subscription->user_id);
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

    public function webhookUpdate(Request $request)
    {
        
    }

    private function _flushUserSubscriptionCache($user_id)
    {
        Cache::forget("user.{$user_id}.subscription");
        Cache::forget("user.{$user_id}.subscription.api");
        Cache::forget("user.{$user_id}.subscription.transaction");
        Cache::forget("user.{$user_id}.subscription.component");
    }
}
