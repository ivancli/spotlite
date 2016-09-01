<?php
namespace App\Repositories\SubscriptionManagement;

use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Libraries\CommonFunctions;
use Exception;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 9:35 AM
 */
class ChargifySubscriptionManager implements SubscriptionManager
{
    use CommonFunctions;

    /**
     * Retrieve a list of products/services from Payment Management Site
     * @return mixed
     */
    public function getProducts()
    {
        $apiURL = env('CHARGIFY_API_URL') . "products.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $products = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $products = json_decode($products);
            return $products;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Retrieve a single product/service from Payment Management Site
     * @param $product_id
     * @return mixed
     */
    public function getProduct($product_id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "products/$product_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $product = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $product = json_decode($product)->product;
            return $product;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Retrieve a list of subscriptions from Payment Management Site
     * @return mixed
     */
    public function getSubscriptions()
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $subscriptions = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $subscriptions = json_decode($subscriptions)->subscription;
            return $subscriptions;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Retrieve a single subscription from Payment Management Site
     * @param $subscription_id
     * @return mixed
     */
    public function getSubscription($subscription_id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $subscription = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $subscription = json_decode($subscription)->subscription;
            return $subscription;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Create a new subscription in Payment Management Site
     * @param $options
     * @return mixed
     */
    public function storeSubscription($options)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $fields = $options;
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Update an existing subscription in Payment Management Site
     * @param $options
     * @return mixed
     */
    public function updateSubscription($options)
    {
        // TODO: Implement updateSubscription() method.
    }

    /**
     * Cancel an existing subscription in Payment Management Site
     * @param $subscription_id
     * @return mixed
     */
    public function cancelSubscription($subscription_id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "delete";
        $result = $this->sendCurl($apiURL, compact(['userpass', 'method']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Retrieve a result preview of downgrade/upgrade from Payment Management Site
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function previewMigration($subscription_id, $options)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id/migrations/preview.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $fields = $options;
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    /**
     * Perform downgrade/upgrade in Payment Management Site
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function setMigration($subscription_id, $options)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id/migrations.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $fields = $options;
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }
}