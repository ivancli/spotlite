<?php
namespace App\Contracts\SubscriptionManagement;

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
     * @param $options
     * @return mixed
     */
    public function updateSubscription($options);

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
}