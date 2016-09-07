<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Invigor\UM\Traits\UMUserTrait;

class User extends Authenticatable
{
    use UMUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = "user_id";
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'verification_code', 'last_login', 'first_login',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_code',
    ];

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Subscription', 'user_id', 'user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany('App\Models\Logs\UserActivityLog', 'user_id', 'user_id');
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Category', 'user_id', 'user_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'user_id', 'user_id');
    }


    /*TODO the following blocks of code need to be refined*/

    public function cachedSubscription()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'subscriptions_for_user_' . $this->$userPrimaryKey;
        return Cache::tags("user_subscriptions")->remember($cacheKey, config()->get('cache.ttl'), function () {
            return $this->subscriptions()->get();
        });
    }

    public function needSubscription()
    {
        return !$this->isStaff() && !$this->hasValidSubscription();

    }

    public function hasValidSubscription()
    {
        $subscriptions = $this->cachedSubscription();
        $isValid = false;
        foreach ($subscriptions as $subscription) {
            $expiryValid = false;
            $cancelledValid = false;
            if (is_null($subscription->expiry_date) || strtotime($subscription->expiry_date) > time()) {
                $expiryValid = true;
            }
            if (is_null($subscription->cancelled_at) || strtotime($subscription->cancelled_at) > time()) {
                $cancelledValid = true;
            }
            if ($expiryValid && $cancelledValid) {
                $isValid = true;
            }
        }
        return $isValid;
    }

    public function isStaff()
    {
        return $this->hasRole(['super_admin', 'tier_1', 'tier_2']);
    }

    public function validSubscriptions()
    {
        $subscriptions = array();
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->isValid()) {
                $subscriptions[] = $subscription;
            }
        }
        return $subscriptions;
    }

    public function latestValidSubscription()
    {
        $subscription = $this->subscriptions->last();
        $isValid = true;
        if (!is_null($subscription)) {
            if (!is_null($subscription->expiry_at)) {
                if (strtotime($subscription->expiry_at) < time()) {
                    $isValid = false;
                }
            } elseif (!is_null($subscription->cancelled_at)) {
                if (strtotime($subscription->cancelled_at) < time()) {
                    $isValid = false;
                }
            }
            if ($isValid == true) {
                return $subscription;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*TODO the code above need to be refined*/
}
