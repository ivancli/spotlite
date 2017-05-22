<?php
namespace App\Repositories\Subscription;

use App\Contracts\Repository\Mailer\MailingAgentContract;
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

    protected $mailingAgentRepo;

    public function __construct(MailingAgentContract $mailingAgentContract)
    {
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    public function generateUpdatePaymentLink(Subscription $subscription, $subscription_id)
    {
        $message = "update_payment--$subscription_id--" . $this->api_share_key($subscription->subscription_location);
        $token = $this->generateToken($message);
        $link = $this->api_domain($subscription->subscription_location) . "update_payment/$subscription_id/" . $token;
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
        if ($user->needSubscription && !is_null($subscription)) {

            $this->updateCreditCardDetails($subscription);

            $user->clearAllCache();

            $apiSubscription = Chargify::subscription($subscription->subscription_location)->get($subscription->api_subscription_id, true);
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
                $subscription->api_product_id = $apiSubscription->product_id;
                $subscription->save();

                $this->mailingAgentRepo->editSubscriber($user->email, array(
                    "CustomFields" => array(
                        array(
                            "Key" => "SubscriptionPlan",
                            "Value" => $apiSubscription->product()->name
                        ),
                    ),
                ));
            }
        }
    }

    public function updateCreditCardDetails(Subscription $subscription)
    {
        $user = $subscription->user;
        $apiSubscription = Chargify::subscription($subscription->subscription_location)->get($subscription->api_subscription_id);
        if (is_null($apiSubscription) || $apiSubscription == false) {
            return false;
        }
        $creditCard = $apiSubscription->paymentProfile();
        $expiryYear = SubscriptionDetail::getDetail($subscription->getKey(), 'CREDIT_CARD_EXPIRY_YEAR');
        $expiryMonth = SubscriptionDetail::getDetail($subscription->getKey(), 'CREDIT_CARD_EXPIRY_MONTH');
        if (!is_null($creditCard)) {
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
        } else {
            return false;
        }

        return compact(['expiryYear', 'expiryMonth']);
    }

    /**
     * @return mixed
     */
    public function getProductList()
    {
        return Cache::rememberForever('au_product_families.products', function () {
            $families = Chargify::productFamily('au')->all();
            $productFamilies = array();
            foreach ($families as $index => $family) {
                //remove starter family
                if ($family->id == 780243) {
                    continue;
                }
                $apiProducts = Chargify::product('au')->allByProductFamily($family->id);
                if (isset($apiProducts->errors) || count($apiProducts) == 0) {
                    continue;
                }
                foreach ($apiProducts as $apiProduct) {
                    if (strpos($apiProduct->handle, 'onboarding') === false) {
                        $product = $apiProduct;
                    }
                }
                if (!isset($product)) {
                    continue;
                }

                $subscriptionPreview = Chargify::subscription('au')->preview(array(
                    "product_id" => $product->id,
                    "customer_attributes" => array(
                        "first_name" => "Spot",
                        "last_name" => "Lite",
                        "email" => "admin@spotlite.com.au",
                        "country" => "AU",
                        "state" => "New South Wales"
                    ),
                    "payment_profile_attributes" => array(
                        "billing_country" => "AU",
                        "billing_state" => "NSW",
                    )
                ));

                $product->criteria = json_decode($product->description);

                if (isset($product->criteria->hidden) && $product->criteria->hidden == 1) {
                    continue;
                }

                $productFamily = $family;
                $productFamily->product = $product;
                $productFamily->preview = $subscriptionPreview;
                $productFamilies[] = $productFamily;

                unset($product);
                unset($apiProducts);
                unset($productFamily);
            }
            $productFamilies = collect($productFamilies);
            $productFamilies = $productFamilies->sortBy('product.price_in_cents')->values();
            return $productFamilies;
        });
    }

    /**
     * @return mixed
     */
    public function getUsProductList()
    {
        return Cache::rememberForever('us_product_families.products', function () {
            $families = Chargify::productFamily('us')->all();
            $productFamilies = array();
            foreach ($families as $index => $family) {
                //remove starter family
                if ($family->id == 780243) {
                    continue;
                }
                $apiProducts = Chargify::product('us')->allByProductFamily($family->id);
                if (isset($apiProducts->errors) || count($apiProducts) == 0) {
                    continue;
                }
                foreach ($apiProducts as $apiProduct) {
                    if (strpos($apiProduct->handle, 'onboarding') === false) {
                        $product = $apiProduct;
                    }
                }
                if (!isset($product)) {
                    continue;
                }

                $subscriptionPreview = Chargify::subscription('us')->preview(array(
                    "product_id" => $product->id,
                    "customer_attributes" => array(
                        "first_name" => "Spot",
                        "last_name" => "Lite",
                        "email" => "admin@spotlite.com.au",
                        "country" => "AU",
                        "state" => "New South Wales"
                    ),
                    "payment_profile_attributes" => array(
                        "billing_country" => "AU",
                        "billing_state" => "NSW",
                    )
                ));

                $product->criteria = json_decode($product->description);

                if (isset($product->criteria->hidden) && $product->criteria->hidden == 1) {
                    continue;
                }

                $productFamily = $family;
                $productFamily->product = $product;
                $productFamily->preview = $subscriptionPreview;
                $productFamilies[] = $productFamily;

                unset($product);
                unset($apiProducts);
                unset($productFamily);
            }
            $productFamilies = collect($productFamilies);
            $productFamilies = $productFamilies->sortBy('product.price_in_cents')->values();
            return $productFamilies;
        });
    }

    /**
     * validating a coupon code based on its product family id
     * @param $subscriptionLocation
     * @param $coupon_code
     * @param $product_family_id
     * @return mixed
     */
    public function validateCoupon($subscriptionLocation, $coupon_code, $product_family_id)
    {
        $coupon = Chargify::coupon($subscriptionLocation)->validate($coupon_code, $product_family_id);
        if (!isset($coupon->errors) && is_null($coupon->archived_at)) {
            return true;
        } else {
            return false;
        }
    }

    private function generateToken($str)
    {
        return substr(sha1($str), 0, 10);
    }

    private function api_domain($subscriptionLocation)
    {
        return config("chargify.{$subscriptionLocation}.api_domain");
    }

    private function api_share_key($subscriptionLocation)
    {
        return config("chargify.{$subscriptionLocation}.api_share_key");
    }
}