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
//        $apiURL = env('CHARGIFY_API_URL') . "products.json";
        $apiURL = config('chargify.api_url') . "products.json";
//        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
//        $apiURL = env('CHARGIFY_API_URL') . "products/$product_id.json";
        $apiURL = config('chargify.api_url') . "products/$product_id.json";
//        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
//        $apiURL = env('CHARGIFY_API_URL') . "subscriptions.json";
        $apiURL = config('chargify.api_url') . "subscriptions.json";
//        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
        $apiURL = config('chargify.api_url') . "subscriptions.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id/migrations/preview.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id/migrations.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
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

    public function generateToken($str)
    {
        return substr(sha1($str), 0, 10);
    }

    public function generateUpdatePaymentLink($subscription_id)
    {
        /* "update_payment--" + subscription id + "--" + share key */
        $message = "update_payment--$subscription_id--" . config("chargify.share_key");
        $token = $this->generateToken($message);
        $link = "https://gmail-sandbox.chargify.com/update_payment/$subscription_id/" . $token;
        return $link;
    }
}