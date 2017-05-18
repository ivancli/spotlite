<?php
namespace App\Contracts\Repository\Subscription;

use App\Models\Subscription;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 9:25 AM
 */
interface SubscriptionContract
{
    /**
     * @return mixed
     */
    public function getProductList();

    /**
     * @return mixed
     */
    public function getUsProductList();

    /**
     * Generate a link for customers to update their payment method
     * https://help.chargify.com/public-pages/self-service-pages.html
     * @param Subscription $subscription
     * @param $subscription_id
     * @return mixed
     * @internal param User $user
     */
    public function generateUpdatePaymentLink(Subscription $subscription, $subscription_id);

    /**
     * Synchronise user subscription status
     * @param User $user
     * @return mixed
     */
    public function syncUserSubscription(User $user);

    /**
     * @param Subscription $subscription
     * @return mixed
     */
    public function updateCreditCardDetails(Subscription $subscription);

    /**
     * validating a coupon code based on its product family id
     * @param $subscriptionLocation
     * @param $coupon_code
     * @param $product_family_id
     * @return mixed
     */
    public function validateCoupon($subscriptionLocation, $coupon_code, $product_family_id);
}