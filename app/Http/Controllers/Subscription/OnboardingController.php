<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/15/2016
 * Time: 9:25 AM
 */

namespace App\Http\Controllers\Subscription;


use App\Contracts\Repository\Subscription\OnboardingContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Invigor\Chargify\Chargify;

class OnboardingController extends Controller
{
    protected $request;
    protected $onboardingRepo;

    public function __construct(Request $request, OnboardingContract $onboardingContract)
    {
        $this->request = $request;
        $this->onboardingRepo = $onboardingContract;
    }

    public function index()
    {
        $user = auth()->user();
        $subscription = $user->apiSubscription;
        $onboardingSubscription = $user->apiOnboardingSubscription;
        $this->onboardingRepo->all();

        $productFamilyId = $subscription->product()->product_family_id;
        $onboardingProduct = $this->onboardingRepo->getByProductFamily($productFamilyId);
        $previewSubscription = $this->onboardingRepo->previewSubscription($onboardingProduct->id);
        return view('subscriptions.onboarding.index')->with(compact(['subscription', 'user', 'onboardingProduct', 'previewSubscription', 'onboardingSubscription']));
    }

    public function store()
    {
        $user = auth()->user();
        if ($user->isStaff) {
            $status = false;
            $errors = array("Staff does not need to subscribe.");
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withErrors($errors);
            }
        }

        $subscription = $user->apiSubscription;
        $product = $subscription->product();
        $productFamily = $product->productFamily();
        $onboardingProduct = $this->onboardingRepo->getByProductFamily($productFamily->id);

        if (is_null($onboardingProduct)) {
            $status = false;
            $errors = array("Onboarding service is not available for this subscription plan.");
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withErrors($errors);
            }
        }

        $onboardingSubscription = $user->apiOnboardingSubscription;
        if (is_null($onboardingSubscription)) {
            /**
             * Subscription
             */
            $onboardingSubscription = $this->onboardingRepo->storeSubscription($onboardingProduct->id, auth()->user());
            if (!isset($onboardingSubscription->errors)) {
                $status = true;
                if ($this->request->ajax()) {
                    if ($this->request->wantsJson()) {
                        return response()->json(compact(['status', 'onboardingSubscription']));
                    } else {
                        return compact(['status', 'onboardingSubscription']);
                    }
                } else {
                    return redirect()->route('subscription.index');
                }
            } else {
                $status = false;
                $errors = $onboardingSubscription->errors;
                if ($this->request->ajax()) {
                    if ($this->request->wantsJson()) {
                        return response()->json(compact(['status', 'errors']));
                    } else {
                        return compact(['status', 'errors']);
                    }
                } else {
                    return redirect()->back()->withErrors($errors);
                }
            }
        } elseif ($onboardingSubscription->product()->initial_charge_in_cents < $onboardingProduct->initial_charge_in_cents) {
            /**
             * Migration
             */
            /*perform migration instead of creation*/
            $onboardingSubscription = $this->onboardingRepo->migrateSubscription($onboardingProduct->id, $user);
            if (!isset($onboardingSubscription->errors)) {
                $status = true;
                if ($this->request->ajax()) {
                    if ($this->request->wantsJson()) {
                        return response()->json(compact(['status', 'onboardingSubscription']));
                    } else {
                        return compact(['status', 'onboardingSubscription']);
                    }
                } else {
                    return redirect()->route('subscription.index');
                }
            } else {
                $status = false;
                $errors = $onboardingSubscription->errors;
                if ($this->request->ajax()) {
                    if ($this->request->wantsJson()) {
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
}