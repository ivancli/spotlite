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

    protected $appends = [
        'preferences'
    ];

    public function subscription()
    {
        return $this->hasOne('App\Models\Subscription', 'user_id', 'user_id');
    }

    public function preferences()
    {
        return $this->hasMany('App\Models\UserPreference', 'user_id', 'user_id');
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


    public function cachedSubscription()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'subscription_for_user_' . $this->$userPrimaryKey;
        return Cache::tags("user_subscription")->remember($cacheKey, config()->get('cache.ttl'), function () {
            return $this->subscription;
        });
    }

    public function cachedAPISubscription()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'api_subscription_for_user_' . $this->$userPrimaryKey;
        if (Cache::tags(['user_api_subscription'])->has($cacheKey)) {
            return Cache::tags(['user_api_subscription'])->get($cacheKey);
        } else {
            return Cache::tags(['user_api_subscription'])->remember($cacheKey, config()->get('cache.ttl'), function () {
                $subscriptionManager = app()->make('App\Contracts\SubscriptionManagement\SubscriptionManager');
                if ($this->hasValidSubscription()) {
                    $subscription = $subscriptionManager->getSubscription($this->cachedSubscription()->api_subscription_id);
                } else {
                    $subscription = false;
                }
                return $subscription;
            });
        }
    }

    public function needSubscription()
    {
        return !$this->isStaff() && !$this->hasValidSubscription();
    }

    public function hasValidSubscription()
    {
        return !is_null($this->cachedSubscription()) && $this->cachedSubscription()->isValid();
    }

    public function isStaff()
    {
        return $this->hasRole(['super_admin', 'tier_1', 'tier_2']);
    }

    public function validSubscription()
    {
        if (!is_null($this->cachedSubscription())) {
            return $this->cachedSubscription()->isValid() ? $this->cachedSubscription() : null;
        } else {
            return null;
        }
    }

    public function getPreferencesAttribute()
    {
        $prefObjects = $this->preferences()->get();
        $preferences = $prefObjects->pluck('value', 'element')->all();
        return $preferences;
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);
        Cache::tags(config()->get('user_subscription'))->flush();
        return $result;
    }
}
