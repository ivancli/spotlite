<?php
namespace App\Repositories\Subscription;

use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Libraries\CommonFunctions;
use App\Models\Subscription;
use App\Models\SubscriptionDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 9:35 AM
 */
class ChargifySubscriptionRepository implements SubscriptionContract
{
    use CommonFunctions;

    /**
     * Retrieve a list of products/services from Payment Management Site
     * @return mixed
     */
    public function getProducts()
    {
        $apiURL = config('chargify.api_url') . "products.json";
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
            $subscriptions = json_decode($subscriptions);
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
     * @param $subscription_id
     * @param $options
     * @return mixed
     */
    public function updateSubscription($subscription_id, $options)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/{$subscription_id}.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $method = "put";
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

    public function addCouponCode($subscription_id, $coupon_code)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/{$subscription_id}/add_coupon.json?code={$coupon_code}";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $method = "post";
        $data_type = 'json';
        $fields = "";
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function getTransactions($subscription_id)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/{$subscription_id}/transactions.json?";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $result = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
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
        $message = "update_payment--$subscription_id--" . config("chargify.share_key");
        $token = $this->generateToken($message);
        $link = config('chargify.api_url') . "update_payment/$subscription_id/" . $token;
        return $link;
    }


    /**
     * (Generate) Retrieve billing portal link
     * https://www.chargify.com/tutorials/billing-portal/
     * @param Subscription $subscription
     * @return mixed
     */
    public function getBillingPortalLink(Subscription $subscription)
    {
        $billingPortalLink = SubscriptionDetail::getDetail($subscription->getKey(), "BILLING_PORTAL_LINK");
        $billingPortalExpiry = SubscriptionDetail::getDetail($subscription->getKey(), "BILLING_PORTAL_EXPIRY");
        $billingPortalFetchCount = SubscriptionDetail::getDetail($subscription->getKey(), "BILLING_PORTAL_Fetch_Count");
        if (is_null($billingPortalLink) || is_null($billingPortalLink->value) || is_null($billingPortalExpiry) || strtotime($billingPortalExpiry->value) < time()) {
            /*TODO request a new link*/
            $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
            $customer_id = $apiSubscription->customer->id;
            if (!is_null($customer_id)) {
                //https://gmail-sandbox.chargify.com/portal/customers/13758704/management_link.json
                //13758704

                $apiURL = config('chargify.api_url') . "portal/customers/$customer_id/management_link.json";
                $userpass = config('chargify.api_key') . ":" . config('chargify.password');
                $method = "get";
                $data_type = 'json';

                $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
                $result = json_decode($result);
                if (!isset($result->errors)) {
                    $url = $result->url;
                    $expiry = date('Y-m-d H:i:s', strtotime($result->expires_at));
                    $fetch_count = $result->fetch_count;
                    SubscriptionDetail::create(array(
                        "element" => "BILLING_PORTAL_LINK",
                        "value" => $url,
                        "subscription_id" => $subscription->getKey()
                    ));
                    SubscriptionDetail::create(array(
                        "element" => "BILLING_PORTAL_EXPIRY",
                        "value" => $expiry,
                        "subscription_id" => $subscription->getKey()
                    ));
                    SubscriptionDetail::create(array(
                        "element" => "BILLING_PORTAL_FETCH_COUNT",
                        "value" => $fetch_count,
                        "subscription_id" => $subscription->getKey()
                    ));
                    return $url;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return $billingPortalLink->value;
        }
    }

    /**
     * Synchronise user subscription status
     * @param User $user
     * @return mixed
     */
    public function syncUserSubscription(User $user)
    {
        $subscription = $user->subscription;
        if (!is_null($subscription)) {

            $this->updateCreditCardDetails($subscription);

            $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
            if (!is_null($apiSubscription) && $apiSubscription !== false) {
                if (!is_null($apiSubscription->canceled_at)) {
                    $subscription->cancelled_at = date('Y-m-d h:i:s', strtotime($apiSubscription->canceled_at));
                } else {
                    $subscription->cancelled_at = null;
                }
                if (!is_null($apiSubscription->expires_at)) {
                    $subscription->expiry_date = date('Y-m-d h:i:s', strtotime($apiSubscription->expires_at));
                } else {
                    $subscription->expiry_date = null;
                }
                if (!is_null($apiSubscription->product)) {
                    $subscription->api_product_id = $apiSubscription->product->id;
                }
                $subscription->save();
            }
        }
    }

    public function updateCreditCardDetails(Subscription $subscription)
    {
        $apiSubscription = $this->getSubscription($subscription->api_subscription_id);
        if (is_null($apiSubscription) || $apiSubscription == false) {
            return false;
        }
        $creditCard = $apiSubscription->credit_card;
        $expiryYear = SubscriptionDetail::getDetail($subscription->getKey(), 'CREDIT_CARD_EXPIRY_YEAR');
        $expiryMonth = SubscriptionDetail::getDetail($subscription->getKey(), 'CREDIT_CARD_EXPIRY_MONTH');

        if (is_null($expiryYear) || is_null($expiryMonth)) {
            if (is_null($expiryYear)) {
                $expiryYear = SubscriptionDetail::create(array(
                    "element" => "CREDIT_CARD_EXPIRY_YEAR",
                    "value" => $creditCard->expiration_year,
                    "subscription_id" => $subscription->getKey()
                ));
            }
            if (is_null($expiryMonth)) {
                $expiryMonth = SubscriptionDetail::create(array(
                    "element" => "CREDIT_CARD_EXPIRY_MONTH",
                    "value" => $creditCard->expiration_month,
                    "subscription_id" => $subscription->getKey()
                ));
            }
        } else {
            $expiryYear->value = $creditCard->expiration_year;
            $expiryYear->save();
            $expiryMonth->value = $creditCard->expiration_month;
            $expiryMonth->save();
        }

        return compact(['expiryYear', 'expiryMonth']);
    }

    public function deletePaymentProfile($subscription_id)
    {
        $subscription = $this->getSubscription($subscription_id);
        $creditCard = $subscription->credit_card;
        if (!is_null($creditCard)) {
            $creditCardID = $creditCard->id;

            $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id/payment_profiles/$creditCardID.json";
            $userpass = config('chargify.api_key') . ":" . config('chargify.password');
            $method = "delete";
            $data_type = 'json';
            $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
            if (!isset($result->errors) || is_null($result->errors)) {
                return true;
            }
        }
        return false;
    }

    public function getProductFamilies()
    {
        $apiURL = config('chargify.api_url') . "product_families.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $productFamilies = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $productFamilies = json_decode($productFamilies);
            return $productFamilies;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function getProductsByProductFamily($product_family_id)
    {
        $apiURL = config('chargify.api_url') . "product_families/$product_family_id/products.json";
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

    public function getComponentsByProductFamily($product_family_id)
    {
        $apiURL = config('chargify.api_url') . "product_families/$product_family_id/components.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $components = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $components = json_decode($components);
            return $components;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function getComponent($product_family_id, $component_id)
    {
        $apiURL = config('chargify.api_url') . "product_families/$product_family_id/components.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $components = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $components = json_decode($components);
            return $components;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function getComponentsBySubscription($subscription_id)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id/components.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $components = $this->sendCurl($apiURL, compact(['userpass']));
        try {
            $components = json_decode($components);
            return $components;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function setComponentBySubscription($subscription_id, $component_id, $quantity)
    {
        if (is_null($quantity)) {
            $quantity = 0;
        }
        $apiURL = config('chargify.api_url') . "subscriptions/{$subscription_id}.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $method = "put";
        $data_type = 'json';
        $component = new \stdClass();
        $component->component = new \stdClass();
        $component->component->component_id = $component_id;
//        $component->component->allocated_quantity = $quantity;
//        $component->component->usage_balance = $quantity;
        $subscription = new \stdClass();
        $subscription->subscription = new \stdClass();
        $subscription->subscription->components = array($component);
        $fields = json_encode($subscription);
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function setComponentAllocationBySubscription($subscription_id, $component_id, $quantity)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/$subscription_id/components/$component_id/allocations.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $method = "post";
        $data_type = 'json';
        $data = new \stdClass();
        $data->allocation = new \stdClass();
        $data->allocation->quantity = $quantity;
        $fields = json_encode($data);
        $result = $this->sendCurl($apiURL, compact(['userpass', 'fields', 'method', 'data_type']));
        try {
            $result = json_decode($result);
            return $result;
        } catch (Exception $e) {
            /*TODO need to handle exception properly*/
            return false;
        }
    }

    public function getPreviewSubscription($product_id, $dummy = true)
    {
        $apiURL = config('chargify.api_url') . "subscriptions/preview.json";
        $userpass = config('chargify.api_key') . ":" . config('chargify.password');
        $method = "post";
        $data_type = 'json';
        $data = new \stdClass();
        $subscription = new \stdClass();
        $customer_attributes = new \stdClass();
        if ($dummy) {
            $customer_attributes->first_name = "Spot";
            $customer_attributes->last_name = "Lite";
            $customer_attributes->email = "admin@spotlite.com.au";
            $customer_attributes->country = "AU";
        }
        $subscription->product_id = $product_id;
        $subscription->customer_attributes = $customer_attributes;
        $data->subscription = $subscription;

        $fields = json_encode($data);
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