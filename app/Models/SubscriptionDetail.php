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
    protected $primaryKey = "subscription_details";
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
}