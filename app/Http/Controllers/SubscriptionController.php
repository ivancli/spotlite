<?php

namespace App\Http\Controllers;

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

        $families = Cache::remember('chargify.product_families', config('cache.subscription_info_cache_expiry'), function () {
            return $this->subscriptionRepo->getProductFamilies();
        });
        $productFamilies = array();

        foreach ($families as $index => $family) {
            $family_id = $family->product_family->id;
            $apiProducts = Cache::remember("chargify.product_families.{$family_id}.products", config('cache.subscription_info_cache_expiry'), function () use ($family_id) {
                return $this->subscriptionRepo->getProductsByProductFamily($family_id);
            });

            if (is_null($apiProducts)) {
                continue;
            }
            $product = $apiProducts[0]->product;
            $apiComponents = Cache::remember("chargify.product_families.{$family_id}.components", config('cache.subscription_info_cache_expiry'), function () use ($family_id) {
                return $this->subscriptionRepo->getComponentsByProductFamily($family_id);
            });

            if (count($apiComponents) == 0) {
                continue;
            }

            $subscriptionPreview = Cache::remember("chargify.product_families.{$family_id}.products.{$product->id}.subscription_preview", config('cache.subscription_info_cache_expiry'), function () use ($product) {
                $subscriptionPreview = $this->subscriptionRepo->getPreviewSubscription($product->id);
                if (!is_null($subscriptionPreview)) {
                    return $subscriptionPreview->subscription_preview;
                }
                return null;
            });

            $component = $apiComponents[0]->component;
            $productFamily = new \stdClass();
            $productFamily->product = $product;
            $productFamily->component = $component;
            $productFamily->preview = $subscriptionPreview;
            $productFamilies[] = $productFamily;
        }
        $productFamilies = collect($productFamilies);
        $productFamilies = $productFamilies->sortBy('product.price_in_cents');
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
                $portalEnabled = !is_null($subscription->customer->portal_customer_created_at);
                if ($portalEnabled) {
                    $portalLink = $this->subscriptionRepo->getBillingPortalLink($sub);
                }
                $transactions = Cache::remember("user.{$user->getKey()}.subscription.transaction", config()->get('cache.ttl'), function () use ($current_sub_id) {
                    return $this->subscriptionRepo->getTransactions($current_sub_id);
                });
                $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($current_sub_id);

                $component = Cache::remember("user.{$user->getKey()}.subscription.component", config()->get('cache.ttl'), function () use ($current_sub_id) {
                    $components = $this->subscriptionRepo->getComponentsBySubscription($current_sub_id);
                    if (count($components) > 0) {
                        return $components[0]->component;
                    }
                    return null;
                });

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
        $product = $this->subscriptionRepo->getProduct($productId);
        $product_family_id = $product->product_family->id;
        $components = Cache::remember("chargify.product_families.$product_family_id.components", config('cache.ttl'), function () use ($product_family_id) {
            return $this->subscriptionRepo->getComponentsByProductFamily($product_family_id);
        });
        $component = $components[0]->component;

        if (!is_null($product)) {
            if ($product->require_credit_card) {
                if (!is_null(auth()->user()->subscription)) {
                    $previousSubscription = auth()->user()->subscription;
                    $previousAPISubscription = $this->subscriptionRepo->getSubscription($previousSubscription->api_subscription_id);
                    if (isset($previousAPISubscription->credit_card)) {
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
                                $result = $this->subscriptionRepo->storeSubscription(json_encode($fields));
                                $this->_flushUserSubscriptionCache($user->getKey());
                                if (isset($result->subscription)) {

                                    $productFamily = $result->subscription->product->product_family;
                                    $apiComponents = Cache::remember("chargify.product_families.{$productFamily->id}.components", config('cache.subscription_info_cache_expiry'), function () use ($productFamily) {
                                        return $this->subscriptionRepo->getComponentsByProductFamily($productFamily->id);
                                    });
                                    $component = $apiComponents[0]->component;
                                    $this->subscriptionRepo->setComponentBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);
                                    $this->subscriptionRepo->setComponentAllocationBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);

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

                $component_quantity = is_null($component->prices[0]->ending_quantity) ? 0 : $component->prices[0]->ending_quantity;
                $encryptedReference = rawurlencode(json_encode($reference));
                $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}&coupon_code={$couponCode}&components[][component_id]={$component->id}&components[][allocated_quantity]={$component_quantity}";
                $this->_flushUserSubscriptionCache($user->getKey());
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
                $result = $this->subscriptionRepo->storeSubscription(json_encode($fields));
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
                        $user->save();

                        $subscription_id = $request->get('id');
                        $subscription = $this->subscriptionRepo->getSubscription($subscription_id);
                        if (!is_null($user->subscription)) {
                            $sub = $user->subscription;
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $component = Cache::remember("user.{$user->getKey()}.subscription.component", config('cache.ttl'), function () use ($subscription) {
                                $components = $this->subscriptionRepo->getComponentsBySubscription($subscription->id);
                                if (count($components) > 0) {
                                    return $components[0]->component;
                                }
                                return null;
                            });
                            $sub->api_component_id = $component->component_id;
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
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $component = Cache::remember("user.{$user->getKey()}.subscription.component", config('cache.ttl'), function () use ($subscription) {
                                $components = $this->subscriptionRepo->getComponentsBySubscription($subscription->id);
                                if (count($components) > 0) {
                                    return $components[0]->component;
                                }
                                return null;
                            });
                            $sub->api_component_id = $component->component_id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();
                            $this->subscriptionRepo->updateCreditCardDetails($sub);
                            event(new SubscriptionCompleted($sub));
                            $this->_flushUserSubscriptionCache($user->getKey());
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

        $families = Cache::remember('chargify.product_families', config('cache.subscription_info_cache_expiry'), function () {
            return $this->subscriptionRepo->getProductFamilies();
        });
        $productFamilies = array();

        foreach ($families as $index => $family) {
            $family_id = $family->product_family->id;
            $apiProducts = Cache::remember("chargify.product_families.{$family_id}.products", config('cache.subscription_info_cache_expiry'), function () use ($family_id) {
                return $this->subscriptionRepo->getProductsByProductFamily($family_id);
            });

            if (is_null($apiProducts)) {
                continue;
            }
            $product = $apiProducts[0]->product;
            $apiComponents = Cache::remember("chargify.product_families.{$family_id}.components", config('cache.subscription_info_cache_expiry'), function () use ($family_id) {
                return $this->subscriptionRepo->getComponentsByProductFamily($family_id);
            });

            if (count($apiComponents) == 0) {
                continue;
            }

            $subscriptionPreview = Cache::remember("chargify.product_families.{$family_id}.products.{$product->id}.subscription_preview", config('cache.subscription_info_cache_expiry'), function () use ($product) {
                $subscriptionPreview = $this->subscriptionRepo->getPreviewSubscription($product->id);
                if (!is_null($subscriptionPreview)) {
                    return $subscriptionPreview->subscription_preview;
                }
                return null;
            });

            $component = $apiComponents[0]->component;
            $productFamily = new \stdClass();
            $productFamily->product = $product;
            $productFamily->component = $component;
            $productFamily->preview = $subscriptionPreview;
            $productFamilies[] = $productFamily;
        }
        $productFamilies = collect($productFamilies);
        $productFamilies = $productFamilies->sortBy('product.price_in_cents');

        event(new SubscriptionEditViewed($subscription));
        return view('subscriptions.edit')->with(compact(['productFamilies', 'chosenAPIProductID', 'subscription']));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        event(new SubscriptionUpdating($subscription));
        $apiSubscription = $this->subscriptionRepo->getSubscription($subscription->api_subscription_id);

        if ($request->has('coupon_code')) {
            $result = $this->subscriptionRepo->addCouponCode($apiSubscription->id, $request->get('coupon_code'));
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

            $result = $this->subscriptionRepo->setMigration($apiSubscription->id, json_encode($fields));
            if ($result != false) {
                if (!is_null($result->subscription)) {
                    $productFamily = $result->subscription->product->product_family;
                    $apiComponents = Cache::remember("chargify.product_families.{$productFamily->id}.components", config('cache.subscription_info_cache_expiry'), function () use ($productFamily) {
                        return $this->subscriptionRepo->getComponentsByProductFamily($productFamily->id);
                    });
                    $component = $apiComponents[0]->component;
//                    $this->subscriptionRepo->setComponentBySubscription($result->subscription->id, $component->id, 0);
                    $this->subscriptionRepo->setComponentBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);
                    $this->subscriptionRepo->setComponentAllocationBySubscription($result->subscription->id, $component->id, $component->prices[0]->ending_quantity);
                    $subscription->api_product_id = $result->subscription->product->id;
                    if (!is_null($result->subscription->canceled_at)) {
                        $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->subscription->canceled_at));
                    }
                    if (!is_null($result->subscription->expires_at)) {
                        $subscription->expiry_date = date('Y-m-d H:i:s', strtotime($result->subscription->expires_at));
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
        event(new SubscriptionCancelling($subscription));
        $apiSubscription = $this->subscriptionRepo->getSubscription($subscription->api_subscription_id);
        if (!is_null($apiSubscription) && is_null($apiSubscription->canceled_at)) {
            if (!$request->has('keep_profile') || $request->get('keep_profile') != '1') {
                $this->subscriptionRepo->deletePaymentProfile($apiSubscription->id);
            }
            $result = $this->subscriptionRepo->cancelSubscription($apiSubscription->id);

            if (!is_null($result->subscription->canceled_at)) {
                $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->subscription->canceled_at));
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

    private function _flushUserSubscriptionCache($user_id)
    {
        Cache::forget("user.{$user_id}.subscription");
        Cache::forget("user.{$user_id}.subscription.api");
        Cache::forget("user.{$user_id}.subscription.transaction");
        Cache::forget("user.{$user_id}.subscription.component");
    }
}
