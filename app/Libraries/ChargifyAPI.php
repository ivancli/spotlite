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
        $products = $this->sendCurl("https://gmail-sandbox.chargify.com/products.json", [], [],
            env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD'));
        try {
            $products = json_decode($products);
            return $products;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSubscription($subscription_id)
    {
        $subscription = $this->sendCurl("https://gmail-sandbox.chargify.com/subscriptions/$subscription_id.json", [], [],
            env('CHARGIFY_API_KEY') . ":" . env('CHARGIFY_PASSWORD'));
        try {
            $subscription = json_decode($subscription);
            return $subscription;
        } catch (Exception $e) {
            return false;
        }
    }
}