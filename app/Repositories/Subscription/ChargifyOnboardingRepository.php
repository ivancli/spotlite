<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/15/2016
 * Time: 9:51 AM
 */

namespace App\Repositories\Subscription;


use App\Contracts\Repository\Subscription\OnboardingContract;
use App\Models\User;
use Invigor\Chargify\Chargify;

class ChargifyOnboardingRepository implements OnboardingContract
{

    public function all()
    {
        $products = Chargify::product()->all();
        $onboardingProducts = array_where($products, function ($key, $product) {
            return strpos($product->handle, 'onboarding') !== false;
        });
        return $onboardingProducts;
    }

    public function getByProductFamily($productFamilyId)
    {
        $productFamily = Chargify::productFamily()->get($productFamilyId);
        $products = $productFamily->products();
        $onboardingProduct = array_where($products, function ($key, $product) {
            return strpos($product->handle, 'onboarding') !== false;
        });
        return array_first($onboardingProduct);
    }

    public function previewSubscription($productId)
    {
        $previewSubscription = Chargify::subscription()->preview(array(
            "product_id" => $productId,
            "customer_attributes" => array(
                "first_name" => "Spot",
                "last_name" => "Lite",
                "email" => "admin@spotlite.com.au",
                "country" => "AU"
            )
        ));

        return $previewSubscription;
    }

    public function storeSubscription($productId, User $user)
    {
        $subscription = $user->subscription;
        $apiSubscription = $user->apiSubscription;
        $paymentProfile = $apiSubscription->paymentProfile();

        $onboardingSubscription = Chargify::subscription()->create(array(
            "product_id" => $productId,
            "customer_id" => $subscription->api_customer_id,
            "payment_profile_id" => $paymentProfile->id,
            "coupon_code" => $apiSubscription->coupon_code,
        ));

        if (!isset($onboardingSubscription->errors)) {
            $subscription->api_onboarding_subscription_id = $onboardingSubscription->id;
            $subscription->save();
            $user->clearCache();
            return $onboardingSubscription;
        } else {
            return $onboardingSubscription;
        }
    }

    public function migrateSubscription($productId, User $user)
    {
        $onboardingSubscription = $user->apiOnboardingSubscription;

        $fields = array(
            "product_id" => $productId,
            "include_coupons" => 1,
            "include_initial_charge" => 1
        );

        $result = Chargify::subscription()->createMigration($onboardingSubscription->id, $fields);
        if (!isset($result->errors)) {
            $user->clearCache();
            return $result;
        } else {
            return $result;
        }
    }
}