<?php
namespace App\Repositories\Subscription;

use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Libraries\CommonFunctions;
use App\Models\Subscription;
use App\Models\SubscriptionDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Chargify;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 9:35 AM
 */
class ChargifySubscriptionRepository implements SubscriptionContract
{
    use CommonFunctions;

    private function generateToken($str)
    {
        return substr(sha1($str), 0, 10);
    }

    public function generateUpdatePaymentLink($subscription_id)
    {
        $message = "update_payment--$subscription_id--" . config("chargify.api_share_key");
        $token = $this->generateToken($message);
        $link = config('chargify.api_domain') . "update_payment/$subscription_id/" . $token;
        return $link;
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
        $apiSubscription = Chargify::subscription()->get($subscription->api_subscription_id);
        if (is_null($apiSubscription) || $apiSubscription == false) {
            return false;
        }
        $creditCard = $apiSubscription->paymentProfile();
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

    /**
     * @return mixed
     */
    public function getProductList()
    {

        $families = Chargify::productFamily()->all();
        $productFamilies = array();
        foreach ($families as $index => $family) {
            $family_id = $family->id;
            $apiProducts = Chargify::product()->allByProductFamily($family->id);

            if (isset($apiProducts->errors) || count($apiProducts) == 0) {
                continue;
            }
            $product = array_first($apiProducts);
            $apiComponents = Chargify::component()->allByProductFamily($family->id);

            if (isset($apiComponents->errors) || count($apiComponents) == 0) {
                continue;
            }

            $subscriptionPreview = Chargify::subscription()->preview(array(
                "product_id" => $product->id,
                "customer_attributes" => array(
                    "first_name" => "Spot",
                    "last_name" => "Lite",
                    "email" => "admin@spotlite.com.au",
                    "country" => "AU"
                )
            ));

            $component = array_first($apiComponents);
            $productFamily = $family;
            $productFamily->product = $product;
            $productFamily->component = $component;
            $productFamily->preview = $subscriptionPreview;
            $productFamilies[] = $productFamily;
        }
        $productFamilies = collect($productFamilies);
        $productFamilies = $productFamilies->sortBy('product.price_in_cents');
        return $productFamilies;
    }
}