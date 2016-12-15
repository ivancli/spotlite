<?php

namespace App\Http\Controllers\Subscription;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\Subscription\OnboardingContract;
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
use App\Jobs\SyncUser;
use App\Models\AppPreference;
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
    protected $onboardingRepo;
    protected $mailingAgentRepo;

    public function __construct(SubscriptionContract $subscriptionContract, MailingAgentContract $mailingAgentContract, OnboardingContract $onboardingContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
        $this->onboardingRepo = $onboardingContract;
        $this->mailingAgentRepo = $mailingAgentContract;
        /*TODO need to handle middleware for each function*/
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewProducts()
    {
        $user = auth()->user();
        if ($user->isStaff || (!is_null($user->subscription) && $user->subscription->isValid())) {
            return redirect()->route('dashboard.index');
        }

        $productFamilies = $this->subscriptionRepo->getProductList();
        event(new SubscriptionViewed());
        return view('subscriptions.subscription_plans')->with(compact(['productFamilies']));
    }

    /**
     * Manage My Subscription - page
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $sub = $user->subscription;
        if (!is_null($sub)) {
            $subscription = $user->apiSubscription;
            $onboardingSubscription = $user->apiOnboardingSubscription;
            if ($subscription != false) {
                $portalLink = Chargify::customer()->getLink($subscription->customer_id);
                $subscriptionTransactions = Chargify::transaction()->allBySubscription($subscription->id);
                if (!is_null($onboardingSubscription)) {
                    $onboardingTransactions = Chargify::transaction()->allBySubscription($onboardingSubscription->id);
                    $transactions = array_merge($subscriptionTransactions, $onboardingTransactions);
                } else {
                    $transactions = $subscriptionTransactions;
                }

                $transactions = collect($transactions);
                $transactions = $transactions->sortBy('created_at');


                $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($subscription->id);

                $onboardingProduct = $this->onboardingRepo->getByProductFamily($subscription->product()->product_family_id);

                event(new SubscriptionManagementViewed());
                return view('subscriptions.index')->with(compact(['sub', 'allSubs', 'subscription', 'updatePaymentLink', 'portalLink', 'transactions', 'onboardingSubscription', 'onboardingProduct']));
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

        if (!isset($product->errors)) {
            if ($product->require_credit_card) {
                if (!is_null(auth()->user()->subscription)) {
                    $previousSubscription = auth()->user()->subscription;
                    $previousAPISubscription = Chargify::subscription()->get($previousSubscription->api_subscription_id);
                    if (!is_null($previousAPISubscription)) {
                        $paymentProfile = $previousAPISubscription->paymentProfile();
                        if (!isset($paymentProfile->errors) && !is_null($paymentProfile)) {
                            if ($paymentProfile->expiration_year > date("Y") || ($paymentProfile->expiration_year == date("Y") && $paymentProfile->expiration_month >= date('n'))) {
                                $newSubscription = Chargify::subscription()->create(array(
                                    "product_id" => $product->id,
                                    "customer_id" => $previousSubscription->api_customer_id,
                                    "payment_profile_id" => $paymentProfile->id,
                                    "coupon_code" => $couponCode,
                                ));
                                $user->clearCache();
//                                $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
                                if (!isset($newSubscription->errors)) {
                                    $previousSubscription->api_product_id = $newSubscription->product_id;
                                    $previousSubscription->api_subscription_id = $newSubscription->id;
                                    $previousSubscription->api_customer_id = $newSubscription->customer_id;
                                    $previousSubscription->cancelled_at = null;
                                    $previousSubscription->save();
                                    return redirect()->route('account.index');
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
                $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}&organization={$user->company_name}&coupon_code={$couponCode}";
                $user->clearCache();
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

                        $criteria = auth()->user()->subscriptionCriteria();
                        $this->mailingAgentRepo->editSubscriber($user->email, array(
                            "CustomFields" => array(
                                array(
                                    "Key" => "SubscribedDate",
                                    "Value" => date("Y/m/d")
                                ),
                                array(
                                    "Key" => "SubscriptionPlan",
                                    "Value" => $product->name
                                ),
                                array(
                                    "Key" => "TrialExpiry",
                                    "Value" => date('Y/m/d', strtotime($newSubscription->trial_ended_at))
                                ),
                                array(
                                    "Key" => "NumberofSites",
                                    "Value" => 0
                                ),
                                array(
                                    "Key" => "NumberofProducts",
                                    "Value" => 0
                                ),
                                array(
                                    "Key" => "NumberofCategories",
                                    "Value" => 0
                                ),
                                array(
                                    "Key" => "MaximumNumberofProducts",
                                    "Value" => isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
                                ),
                                array(
                                    "Key" => "MaximumNumberofSites",
                                    "Value" => isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
                                ),
                                array(
                                    "Key" => "LastLoginDate",
                                    "Value" => date('Y/m/d')
                                ),
                            ),
                            'Resubscribe' => true
                        ));

                        event(new SubscriptionCompleted($sub));
                        $user->clearCache();
                        $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);
                        return redirect()->route('account.index');
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
//                        $user->save();

                        $subscription_id = $request->get('id');
                        $subscription = Chargify::subscription()->get($subscription_id);
                        $product = $subscription->product();
                        if (!is_null($user->subscription)) {
                            $sub = $user->subscription;
                            $sub->api_product_id = $subscription->product_id;
                            $sub->api_customer_id = $subscription->customer_id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();

                            $this->subscriptionRepo->updateCreditCardDetails($sub);

                            $this->mailingAgentRepo->editSubscriber($user->email, array(
                                "CustomFields" => array(
                                    array(
                                        "Key" => "LastSubscriptionUpdatedDate",
                                        "Value" => date("Y/m/d")
                                    ),
                                    array(
                                        "Key" => "TrialExpiry",
                                        "Value" => date('Y/m/d', strtotime($subscription->trial_ended_at))
                                    ),
                                    array(
                                        "Key" => "SubscriptionCancelledDate",
                                        "Value" => null
                                    ),
                                    array(
                                        "Key" => "SubscriptionPlan",
                                        "Value" => $product->name
                                    ),
                                )
                            ));

                            event(new SubscriptionUpdated($sub));
                            $user->clearCache();
                            $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);
                            return redirect()->route('account.index');
//                            }
                        } else {
                            /* create subscription record in DB */
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $subscription->product_id;
                            $sub->api_customer_id = $subscription->customer_id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
                            $sub->save();
                            $this->subscriptionRepo->updateCreditCardDetails($sub);

                            $criteria = auth()->user()->subscriptionCriteria();
                            $this->mailingAgentRepo->editSubscriber($user->email, array(
                                "CustomFields" => array(
                                    array(
                                        "Key" => "SubscribedDate",
                                        "Value" => date("Y/m/d")
                                    ),
                                    array(
                                        "Key" => "SubscriptionPlan",
                                        "Value" => $product->name
                                    ),
                                    array(
                                        "Key" => "TrialExpiry",
                                        "Value" => date('Y/m/d', strtotime($subscription->trial_ended_at))
                                    ),
                                    array(
                                        "Key" => "NumberofSites",
                                        "Value" => 0
                                    ),
                                    array(
                                        "Key" => "NumberofProducts",
                                        "Value" => 0
                                    ),
                                    array(
                                        "Key" => "NumberofCategories",
                                        "Value" => 0
                                    ),
                                    array(
                                        "Key" => "MaximumNumberofProducts",
                                        "Value" => isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
                                    ),
                                    array(
                                        "Key" => "MaximumNumberofSites",
                                        "Value" => isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
                                    ),
                                    array(
                                        "Key" => "LastLoginDate",
                                        "Value" => date('Y/m/d')
                                    ),
                                    array(
                                        "Key" => "SubscriptionCancelledDate",
                                        "Value" => null
                                    ),
                                ),
                                "Resubscribe" => true
                            ));
                            event(new SubscriptionCompleted($sub));
                            $user->clearCache();
                            $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);
                            return redirect()->route('dashboard.index');
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
        auth()->user()->clearCache();
        $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
        return redirect()->route('account.index');
    }

    public function edit($id)
    {

        $subscription = auth()->user()->subscription;
        /*TODO validate the $subscription*/

        $chosenAPIProductID = $subscription->api_product_id;
        $chosenAPIProduct = Chargify::product()->get($subscription->api_product_id);
        //load all products from Chargify
        $productFamilies = $this->subscriptionRepo->getProductList();
        event(new SubscriptionEditViewed($subscription));
        return view('subscriptions.edit')->with(compact(['productFamilies', 'chosenAPIProductID', 'subscription', 'chosenAPIProduct']));
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

        /* validation*/
        $targetProduct = Chargify::product()->get($request->get('api_product_id'));
        if (!is_null($targetProduct->description)) {
            $targetCriteria = json_decode($targetProduct->description);
            if (!is_null($targetCriteria)) {
                $productLimit = $targetCriteria->product;
                $siteLimit = $targetCriteria->site;
                $status = true;
                $errors = array();
                if (!is_null($productLimit) && $productLimit != 0 && $productLimit < auth()->user()->products()->count()) {
                    $status = false;
                    $errors = array("Please reduce number of products to meet target subscription plans criteria.");
                }
                if (!is_null($siteLimit) && $siteLimit != 0) {
                    foreach (auth()->user()->products as $product) {
                        if ($siteLimit < $product->sites()->count()) {
                            $status = false;
                            $errors = array("Please reduce number of product URLs to meet target subscription plans criteria.");
                            break;
                        }
                    }
                }
                if ($status == false) {
                    if ($request->ajax()) {
                        if ($request->wantsJson()) {
                            return response()->json(compact(['status', 'errors']));
                        } else {
                            return compact(['status', 'errors']);
                        }
                    } else {
                        return redirect()->back()->withErrors($errors);
                    }
                }
            }
        }

        /**
         * add coupon code
         */
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
            if (!isset($result->errors)) {
                $subscription->api_product_id = $result->product_id;
                if (!is_null($result->canceled_at)) {
                    $subscription->cancelled_at = date('Y-m-d H:i:s', strtotime($result->canceled_at));
                }
                if (!is_null($result->expires_at)) {
                    $subscription->expiry_date = date('Y-m-d H:i:s', strtotime($result->expires_at));
                }
                $subscription->save();
                $newSubscription = $result;
                auth()->user()->clearCache();
                $criteria = auth()->user()->subscriptionCriteria();
                if ($criteria->my_price == false) {
                    foreach (auth()->user()->sites as $site) {
                        $site->my_price = 'n';
                        $site->save();
                    }
                }
                $this->mailingAgentRepo->editSubscriber(auth()->user()->email, array(
                    "CustomFields" => array(
                        array(
                            "Key" => "LastSubscriptionUpdatedDate",
                            "Value" => date("Y/m/d")
                        ),
                        array(
                            "Key" => "TrialExpiry",
                            "Value" => date('Y/m/d', strtotime($newSubscription->trial_ended_at))
                        ),
                        array(
                            "Key" => "MaximumNumberofProducts",
                            "Value" => isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
                        ),
                        array(
                            "Key" => "MaximumNumberofSites",
                            "Value" => isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
                        ),
                        array(
                            "Key" => "SubscriptionPlan",
                            "Value" => $newSubscription->product()->name
                        ),
                        array(
                            "Key" => "SubscriptionCancelledDate",
                            "Value" => null
                        ),
                    )
                ));
                $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
                event(new SubscriptionUpdated($subscription));
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
        $trialKey = "";
        if (!is_null($apiSubscription->trial_ended_at)) {
            $trialEndedTimeStamp = strtotime($apiSubscription->trial_ended_at);
            $nowTimestamp = time();
            if ($trialEndedTimeStamp >= $nowTimestamp) {
                $trialKey = "CancelledBeforeEndofTrial";
            } else {
                $trialKey = "CancelledAfterEndofTrial";
            }
        }
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


                /* update cancel field in campaign monitor*/
                $this->mailingAgentRepo->editSubscriber(auth()->user()->email, array(
                    "CustomFields" => array(
                        array(
                            "Key" => "SubscriptionCancelledDate",
                            "Value" => date("Y/m/d")
                        ),
                        array(
                            "Key" => "MaximumNumberofProducts",
                            "Value" => null
                        ),
                        array(
                            "Key" => "MaximumNumberofSites",
                            "Value" => null
                        ),
                        array(
                            "Key" => $trialKey,
                            "Value" => "true"
                        )
                    )
                ));
                auth()->user()->clearCache();
                $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
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
        Chargify::subscription()->flushAll();

        /*reserve the task*/
        AppPreference::setSyncReserved();
        AppPreference::setSyncLastReservedAt();

        $userSyncTime = AppPreference::getSyncTimes();
        $currentHour = intval(date("H"));
        if (in_array($currentHour, $userSyncTime)) {
            $users = User::all();
            foreach ($users as $user) {
                $user->clearCache();
                dispatch((new SyncUser($user))->onQueue("syncing"));
            }
        }
        AppPreference::setSyncReserved('n');
    }

    public function productFamilies(Request $request)
    {
        $productFamilies = $this->subscriptionRepo->getProductList();
        $status = true;
        if ($request->has('callback')) {
            return response()->json(compact(['productFamilies', 'status']))->setCallback($request->get('callback'));
        } else if ($request->wantsJson()) {
            return response()->json(compact(['productFamilies', 'status']));
        } else {
            return compact(['productFamilies', 'status']);
        }
    }
}
