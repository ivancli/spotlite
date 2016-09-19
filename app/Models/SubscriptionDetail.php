<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/15/2016
 * Time: 4:02 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SubscriptionDetail extends Model
{
    protected $table = "subscription_details";
    protected $primaryKey = "subscription_detail_id";
    protected $fillable = [
        "element", "value", "subscription_id"
    ];


    public function subscription()
    {
        return $this->belongsTo('App\Models\Subscription', 'subscription_id', 'subscription_id');
    }

    public static function getDetails($subscription_id)
    {
        return (new static)->where("subscription_id", $subscription_id)->get();
    }

    public static function getDetail($subscription_id, $key)
    {
        return (new static)->where("subscription_id", $subscription_id)->where("element", $key)->first();
    }

    public static function getCreditCardExpiryYear($subscription_id)
    {
        return (new static)->getDetail($subscription_id, "CREDIT_CARD_EXPIRY_YEAR");
    }

    public static function getCreditCardExpiryMonth($subscription_id)
    {
        return (new static)->getDetail($subscription_id, "CREDIT_CARD_EXPIRY_MONTH");
    }
}