<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Invigor\Chargify\Chargify;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 5:09 PM
 */
class Subscription extends Model
{
    public $timestamps = false;
    protected $primaryKey = "subscription_id";
    protected $fillable = [
        'api_product_id', 'api_custom_id', 'api_subscription_id', 'expiry_date', 'cancelled_at', 'subscription_location',
    ];

    protected $appends = [
        'isPastDue', 'isCancelled'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function subscriptionDetails()
    {
        return $this->hasMany('App\Models\SubscriptionDetail', 'subscription_id', 'subscription_id');
    }

    public function isValid()
    {
        return Cache::tags(['users', "user_" . $this->getKey()])->remember('is_valid', config('cache.ttl'), function () {
            $subscription = Chargify::subscription($this->subscription_location)->get($this->api_subscription_id);
            return $subscription->state == 'active' || $subscription->state == 'trialing';
        });
    }

    public function getIsPastDueAttribute()
    {
        return Cache::tags(['users', "user_" . $this->getKey()])->remember('is_past_due', config('cache.ttl'), function () {
            $subscription = Chargify::subscription($this->subscription_location)->get($this->api_subscription_id);
            return !is_null($subscription) && $subscription->state == 'past_due';
        });
    }

    public function getIsCancelledAttribute()
    {

        return Cache::tags(['users', "user_" . $this->getKey()])->remember('is_cancelled', config('cache.ttl'), function () {
            $subscription = Chargify::subscription($this->subscription_location)->get($this->api_subscription_id);
            return !is_null($subscription) && $subscription->state == 'canceled';
        });
    }

    public function creditCardExpiringWithinMonthOrExpired($month = 1)
    {
//        if ($previousAPICreditCard->expiration_year > date("Y") || ($previousAPICreditCard->expiration_year == date("Y") && $previousAPICreditCard->expiration_month >= date('n'))) {
        $exYear = SubscriptionDetail::getCreditCardExpiryYear($this->getKey());
        $exMonth = SubscriptionDetail::getCreditCardExpiryMonth($this->getKey());
        $yearDiff = -1;
        $monthDiff = -1;
        if (!is_null($exYear)) {
            $yearDiff = date("Y") - $exYear->value;
        }
        if (!is_null($exMonth)) {
            $monthDiff = date("n") - $exMonth->value;
        }
        $totalDiff = $yearDiff * 12 + $monthDiff;
        return $totalDiff * -1 <= $month;
    }
}