<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 5:09 PM
 */
class Subscription extends Model
{
    protected $primaryKey = "subscription_id";
    protected $fillable = [
        'api_product_id', 'api_custom_id', 'api_subscription_id', 'expiry_date', 'cancelled_at',
    ];
    public $timestamps = false;

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
        if (!is_null($this->expiry_date)) {
            return strtotime($this->expiry_date) > time();
        } else {
            return is_null($this->cancelled_at) || $this->cancelled_at > time();
        }
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

    public function save(array $options = [])
    {
        $result = parent::save($options);
        Cache::tags(Config::get('user_subscriptions'))->flush();
        return $result;
    }

    public function delete(array $options = [])
    {
        $result = parent::delete($options);
        Cache::tags(Config::get('user_subscriptions'))->flush();
        return $result;
    }
}