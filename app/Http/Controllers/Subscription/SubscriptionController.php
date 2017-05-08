<?php

namespace App\Http\Controllers\Subscription;

use App\Contracts\Repository\Mailer\MailingAgentContract;
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
    protected $mailingAgentRepo;

    public function __construct(SubscriptionContract $subscriptionContract, MailingAgentContract $mailingAgentContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
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
        if (!$user->needSubscription || (!is_null($user->subscription) && $user->subscription->isValid())) {
            return redirect()->route('dashboard.index');
        }
        $productFamilies = $this->subscriptionRepo->getProductList();
        event(new SubscriptionViewed());
        if (!is_null($user->apiSubscription) && $user->apiSubscription->state == 'past_due') {
            $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($user->apiSubscription->id);
            return view('subscriptions.subscription_trial_ended')->with(compact(['productFamilies', 'updatePaymentLink']));
        }
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
            if ($subscription != false) {
                $portalLink = Chargify::customer()->getLink($subscription->customer_id);
                $subscriptionTransactions = Chargify::transaction()->allBySubscription($subscription->id);
                $transactions = $subscriptionTransactions;

                $transactions = collect($transactions);
                $transactions = $transactions->sortBy('created_at');


                $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($subscription->id);

                event(new SubscriptionManagementViewed());
                return view('subscriptions.index')->with(compact(['sub', 'allSubs', 'subscription', 'updatePaymentLink', 'portalLink', 'transactions']));
            } else {
                abort(403);
                return false;
            }
        } else {

        }
    }

    public function confirm(Request $request)
    {
        if ($request->has('api_product_id')) {
            // preview subscription
            $apiProductId = $request->get('api_product_id');
            $user = auth()->user();

            if (is_null($user->apiSubscription) || $apiProductId != $user->apiSubscription->product_id) {
                /*TODO show subscription preview next billing manifest*/
                $preview = Chargify::subscription()->preview(array(
                    "product_id" => $apiProductId,
                    "customer_attributes" => array(
                        "first_name" => "Spot",
                        "last_name" => "Lite",
                        "email" => "admin@spotlite.com.au",
                        "country" => "AU",
                        "state" => "New South Wales"
                    ),
                    "payment_profile_attributes" => array(
                        "billing_country" => "AU",
                        "billing_state" => "NSW",
                    )
                ));
                $subscriptionPreview = $preview->next_billing_manifest;
            } else {
                /*TODO show renewal preview*/
                $subscriptionPreview = Chargify::subscription()->previewRenew($user->apiSubscription->id);
            }

            $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($user->apiSubscription->id);
            $product = Chargify::product()->get($request->get('api_product_id'));
            $product->criteria = json_decode($product->description);
            return view('subscriptions.confirm')->with(compact(['product', 'subscriptionPreview', 'updatePaymentLink']));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        event(new SubscriptionCreating());

        $user = auth()->user();
        /* user does not have subscription */
        if (is_null($user->apiSubscription)) {
            abort(403);
            return false;
        }

        $user->clearAllCache();
        if (!is_null($user->subscription)) {
            if ($request->get('api_product_id') != $user->apiSubscription->product_id) {
                $product = Chargify::product()->get($request->get('api_product_id'));
                Chargify::subscription()->update($user->apiSubscription->id, array(
                    "product_handle" => $product->handle
                ));
            }
            /* check if payment profile exists*/
            if (is_null($user->apiSubscription->credit_card_id)) {
                // if no payment profile, redirect user to update credit card page
                return redirect()->to($this->subscriptionRepo->generateUpdatePaymentLink($user->apiSubscription->id));
            } else {
                // if there is payment profile, try to reactivate subscription
                $result = Chargify::subscription()->reactivate($user->apiSubscription->id);
                /* check if reactivation succeed*/
                if (isset($result->errors)) {
                    //cannot reactivate subscription, most likely because credit card has insufficient fund or not authorised to process payment.
                    /* return back to error page*/
                    /* suggest either change credit card or make sure credit has sufficient fund and try again*/
                    $subscriptionErrors = $result->errors;
                    $updatePaymentLink = $this->subscriptionRepo->generateUpdatePaymentLink($user->apiSubscription->id);
                    return redirect()->back()->withErrors(compact(['subscriptionErrors']));
                } else {
                    $user->clearAllCache();
                    $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
                    $subscription = $user->subscription;
                    $subscription->api_product_id = $user->apiSubscription->product()->id;
                    $subscription->api_subscription_id = $user->apiSubscription->id;
                    $subscription->api_customer_id = $user->apiSubscription->customer_id;
                    $subscription->cancelled_at = null;
                    $subscription->save();

                    /*!!!!!!!!!!!!TODO have a look at finalise method, that's something we need to add here*/


                    /*TODO redirect to subscription success msg page*/
                    $msg = "Thank you for your subscription and here is the subscription details.";
                    return redirect()->route('account.index')->with(compact(['msg']));
                }

            }
        } else {
            $product = Chargify::product()->get($request->get('api_product_id'));
            $reference = array(
                "user_id" => $user->getKey()
            );
            $encryptedReference = json_encode($reference);
            /* create subscription in chargify */
            $fields = array(
                "product_id" => $product->id,
                "customer_attributes" => array(
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "email" => $user->email,
                    "reference" => $encryptedReference
                ),
            );
            $result = Chargify::subscription()->create($fields);

            $msg = "Thank you for your subscription and here is the subscription details.";
            return redirect()->route('account.index')->with(compact(['msg']));
        }

    }

//    public function finalise(Request $request)
//    {
//        if (!$request->has('ref') || !$request->has('id')) {
//            abort(403, "unauthorised access");
//        } else {
//            $reference = $request->get('ref');
//            $reference = json_decode($reference);
//            try {
//                if (property_exists($reference, 'user_id') && property_exists($reference, 'verification_code')) {
//                    $user = User::findOrFail($reference->user_id);
//                    if ($user->verification_code == $reference->verification_code) {
//                        $user->verification_code = null;
////                        $user->save();
//
//                        $subscription_id = $request->get('id');
//                        $subscription = Chargify::subscription()->get($subscription_id);
//                        $product = $subscription->product();
//                        if (!is_null($user->subscription)) {
//                            $sub = $user->subscription;
//                            $sub->api_product_id = $subscription->product_id;
//                            $sub->api_customer_id = $subscription->customer_id;
//                            $sub->api_subscription_id = $subscription->id;
//                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
//                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
//                            $sub->save();
//
//                            $this->subscriptionRepo->updateCreditCardDetails($sub);
//
//                            $this->mailingAgentRepo->editSubscriber($user->email, array(
//                                "CustomFields" => array(
//                                    array(
//                                        "Key" => "LastSubscriptionUpdatedDate",
//                                        "Value" => date("Y/m/d")
//                                    ),
//                                    array(
//                                        "Key" => "TrialExpiry",
//                                        "Value" => date('Y/m/d', strtotime($subscription->trial_ended_at))
//                                    ),
//                                    array(
//                                        "Key" => "SubscriptionCancelledDate",
//                                        "Value" => null
//                                    ),
//                                    array(
//                                        "Key" => "SubscriptionPlan",
//                                        "Value" => $product->name
//                                    ),
//                                    array(
//                                        "Key" => "CancelledBeforeEndofTrial",
//                                        "Value" => null
//                                    ),
//                                    array(
//                                        "Key" => "CancelledAfterEndofTrial",
//                                        "Value" => null
//                                    ),
//                                )
//                            ));
//
//                            event(new SubscriptionUpdated($sub));
//                            $user->clearAllCache();
//                            $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);
//                            return redirect()->route('account.index');
////                            }
//                        } else {
//                            /* create subscription record in DB */
//                            $sub = new Subscription();
//                            $sub->user_id = $user->getKey();
//                            $sub->api_product_id = $subscription->product_id;
//                            $sub->api_customer_id = $subscription->customer_id;
//                            $sub->api_subscription_id = $subscription->id;
//                            $sub->expiry_date = is_null($subscription->expires_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->expires_at));
//                            $sub->cancelled_at = is_null($subscription->canceled_at) ? null : date('Y-m-d H:i:s', strtotime($subscription->canceled_at));
//                            $sub->save();
//                            $this->subscriptionRepo->updateCreditCardDetails($sub);
//
//                            $criteria = auth()->user()->subscriptionCriteria();
//                            $this->mailingAgentRepo->editSubscriber($user->email, array(
//                                "CustomFields" => array(
//                                    array(
//                                        "Key" => "SubscribedDate",
//                                        "Value" => date("Y/m/d")
//                                    ),
//                                    array(
//                                        "Key" => "SubscriptionPlan",
//                                        "Value" => $product->name
//                                    ),
//                                    array(
//                                        "Key" => "TrialExpiry",
//                                        "Value" => date('Y/m/d', strtotime($subscription->trial_ended_at))
//                                    ),
//                                    array(
//                                        "Key" => "NumberofSites",
//                                        "Value" => 0
//                                    ),
//                                    array(
//                                        "Key" => "NumberofProducts",
//                                        "Value" => 0
//                                    ),
//                                    array(
//                                        "Key" => "NumberofCategories",
//                                        "Value" => 0
//                                    ),
//                                    array(
//                                        "Key" => "MaximumNumberofProducts",
//                                        "Value" => isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
//                                    ),
//                                    array(
//                                        "Key" => "MaximumNumberofSites",
//                                        "Value" => isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
//                                    ),
//                                    array(
//                                        "Key" => "LastLoginDate",
//                                        "Value" => date('Y/m/d')
//                                    ),
//                                    array(
//                                        "Key" => "SubscriptionCancelledDate",
//                                        "Value" => null
//                                    ),
//                                    array(
//                                        "Key" => "CancelledBeforeEndofTrial",
//                                        "Value" => null
//                                    ),
//                                    array(
//                                        "Key" => "CancelledAfterEndofTrial",
//                                        "Value" => null
//                                    ),
//                                ),
//                                "Resubscribe" => true
//                            ));
//                            event(new SubscriptionCompleted($sub));
//                            $user->clearAllCache();
//                            $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);
//                            return redirect()->route('dashboard.index');
//                        }
//                    } else {
//                        abort(403, "unauthorised access");
//                        return false;
//                    }
//                } else {
//                    abort(404, "page not found");
//                    return false;
//                }
//
//            } catch (ModelNotFoundException $e) {
//                abort(404, "page not found");
//                return false;
//            }
//        }
//    }

    public function externalUpdate(Request $request)
    {
        $ref = json_decode($request->get('ref'));
        if (empty($ref)) {
            auth()->user()->clearAllCache();
            return redirect()->route('product.index');
        }
        $user_id = $ref->user_id;
        if (auth()->user()->getKey() != $user_id) {
            abort(403);
        }

        $this->subscriptionRepo->syncUserSubscription(auth()->user());
        $user = auth()->user();
        $user->clearAllCache();
        Cache::tags(["subscriptions.{$user->apiSubscription->id}"])->flush();
        $this->mailingAgentRepo->updateNextLevelSubscriptionPlan($user);

        //TODO need testing
        if (!$user->subscription->isValid()) {
            /*reactivate subscription if subscription state is not trialing or active*/
            $result = Chargify::subscription()->reactivate($user->apiSubscription->id);
            $user->clearAllCache();
            $msg = "Thank you for your subscription and here is the subscription details.";
            return redirect()->route('account.index')->with(compact(['msg']));
        } else {
            $msg = "Your account has been updated.";
            return redirect()->route('account.index')->with(compact(['msg']));
        }
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
//        if (is_null($apiSubscription->credit_card_id)) {
//            return redirect()->to($this->subscriptionRepo->generateUpdatePaymentLink($apiSubscription->id));
//        }

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
        if (is_null($apiSubscription->credit_card_id)) {

            $product = Chargify::product()->get($request->get('api_product_id'));
            Chargify::subscription()->update($apiSubscription->id, array(
                "product_handle" => $product->handle
            ));
            auth()->user()->clearAllCache();
            $subscription = auth()->user()->subscription;
            $subscription->api_product_id = $request->get('api_product_id');
            $subscription->save();
            $this->mailingAgentRepo->updateNextLevelSubscriptionPlan(auth()->user());
            $status = true;
            return compact(['status', 'subscription']);
            //current subscription no payment method
//            return $this->store($request);
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
                auth()->user()->clearAllCache();
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
                        array(
                            "Key" => "CancelledBeforeEndofTrial",
                            "Value" => null
                        ),
                        array(
                            "Key" => "CancelledAfterEndofTrial",
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
            } else {
                $errors = ["Unable to change subscription plan, please make sure the provided credit card has sufficient fund. Alternatively you can update your card and try again."];
                $status = false;
                return compact(['status', 'errors']);
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
                            "Key" => "MaximumNumberofProducts",
                            "Value" => null
                        ),
                        array(
                            "Key" => "MaximumNumberofSites",
                            "Value" => null
                        ),
                        array(
                            "Key" => "SubscriptionCancelledDate",
                            "Value" => date("Y/m/d")
                        ),
                        array(
                            "Key" => $trialKey,
                            "Value" => "true"
                        )
                    )
                ));
                if (!$request->has('keep_profile') || $request->get('keep_profile') != '1') {
                    $this->mailingAgentRepo->deleteSubscriber(auth()->user()->email);
                }
                auth()->user()->clearAllCache();
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
                $user->clearAllCache();
                dispatch((new SyncUser($user))->onQueue("syncing"));
            }
        }
        AppPreference::setSyncReserved('n');
    }

    public function productFamiliesAU(Request $request)
    {
        $request->session()->put('subscription_location', 'au');
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

    public function productFamiliesUS(Request $request)
    {
        $request->session()->put('subscription_location', 'us');
        $productFamilies = $this->subscriptionRepo->getUsProductList();
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
