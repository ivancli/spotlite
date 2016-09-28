<?php
namespace App\Contracts\SubscriptionManagement;

use App\Models\Subscription;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 9:25 AM
 */
interface SubscriptionManager
{
    /**
     * Retrieve a list of products/services from Payment Management Site
     * @return mixed
     */
    public function getProducts();

    /**
     * Retrieve a single product/service from Payment Management Site
     * @param $product_id
     * @return mixed
     */
    public function getProduct($product_id);

    /**
     * Retrieve a list of subscriptions from Payment Management Site
     * @return mixed
     */
    public function getSubscriptions();

    /**
     * Retrieve a single subscription from Payment Management Site
     * @param $subscription_id
     * @return mixed
     */
    public function getSubscription($subscription_id);

    /**
     * Create a new subscription in Payment Management Site
     * @param $options
     * @return mixed
     */
    public function storeSubscription($options);

    /**
     * Update an existing subscription in Payment Management Site
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function updateSubscription($subscription_id, $options);

    /**
     * Cancel an existing subscription in Payment Management Site
     * @param $subscription_id
     * @return mixed
     */
    public function cancelSubscription($subscription_id);

    /**
     * Retrieve a result preview of downgrade/upgrade from Payment Management Site
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function previewMigration($subscription_id, $options);

    /**
     * Perform downgrade/upgrade in Payment Management Site
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function setMigration($subscription_id, $options);

    /**
     * Generate a link for customers to update their payment method
     * https://help.chargify.com/public-pages/self-service-pages.html
     * @param $subscription_id
     * @return mixed
     */
    public function generateUpdatePaymentLink($subscription_id);

    /**
     * (Generate) Retrieve billing portal link
     * https://www.chargify.com/tutorials/billing-portal/
     * @param Subscription $subscription
     * @return mixed
     */
    public function getBillingPortalLink(Subscription $subscription);

    /**
     * Synchronise user subscription status
     * @param User $user
     * @return mixed
     */
    public function syncUserSubscription(User $user);

    public function updateCreditCardDetails(Subscription $subscription);

    public function deletePaymentProfile($subscription_id);
}