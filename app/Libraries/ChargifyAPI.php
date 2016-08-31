<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 1:53 PM
 */

namespace App\Libraries;


use Mockery\CountValidator\Exception;

trait ChargifyAPI
{
    use CommonFunctions;

    public function getProducts()
    {
        $apiURL = env('CHARGIFY_API_URL') . "products.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $products = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $products = json_decode($products);
            return $products;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProduct($id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "products/$id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $product = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $product = json_decode($product)->product;
            return $product;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSubscription($subscription_id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $subscription = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $subscription = json_decode($subscription)->subscription;
            return $subscription;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setSubscription($fields)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getInvoiceBySubscriptionID($subscription_id)
    {
        //https://<subdomain>.chargify.com/invoices.<format>?subscription_id=<sub_id>
        $apiURL = env('CHARGIFY_API_URL') . "invoices/$subscription_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $subscription = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $subscription = json_decode($subscription)->subscription;
            return $subscription;
        } catch (Exception $e) {
            return false;
        }
    }

    public function cancelSubscriptionBySubscriptionID($subscription_id)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "delete";
        $result = $this->sendCurl($apiURL, compact(['userpass', 'method']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function previewMigration($subscription_id, $fields)
    {
        //https://[@subdomain].chargify.com/subscriptions/[@subscription.id]/migrations/preview.json
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id/migrations/preview.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function migrateSubscription($subscription_id, $fields)
    {
        $apiURL = env('CHARGIFY_API_URL') . "subscriptions/$subscription_id/migrations.json";
        $userpass = env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD');
        $method = "post";
        $data_type = 'json';
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

}