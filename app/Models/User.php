<?php

namespace App\Models;

use App\Models\Dashboard\Dashboard;
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
        'title', 'first_name', 'last_name', 'email', 'phone', 'password', 'verification_code', 'last_login', 'first_login',
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

    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'user_id', 'user_id');
    }

    public function dashboards()
    {
        return $this->hasMany('App\Models\Dashboard\Dashboard', 'user_id', 'user_id')->orderBy("dashboard_order", "asc");
    }

    public function nonHiddenDashboard()
    {
        return $this->hasMany('App\Models\Dashboard\Dashboard', 'user_id', 'user_id')->where("is_hidden", "!=", "y")->orderBy("dashboard_order", "asc");
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * indirect relationship
     */

    public function categoryReportTasks()
    {
        return $this->hasManyThrough('App\Models\ReportTask', 'App\Models\Category', 'user_id', 'report_task_owner_id', 'user_id')->where('report_task_owner_type', 'category');
    }

    public function productReportTasks()
    {
        return $this->hasManyThrough('App\Models\ReportTask', 'App\Models\Product', 'user_id', 'report_task_owner_id', 'user_id')->where('report_task_owner_type', 'product');
    }

    public function productAlerts()
    {
        return $this->hasManyThrough('App\Models\Alert', 'App\Models\Product', 'user_id', 'alert_owner_id', 'user_id')->where('alert_owner_type', 'product');
    }

    public function sites()
    {
        return $this->hasManyThrough('App\Models\Site', 'App\Models\Product', 'user_id', 'product_id', 'user_id');
    }

//----------------------------------------------------------------------------------------------------------------------

    public function preference($key)
    {
        $preference = $this->hasMany('App\Models\UserPreference', 'user_id', 'user_id')->where('element', $key)->first();
        if (is_null($preference)) {
            return null;
        } else {
            return $preference->value;
        }
    }

    public function cachedSubscription()
    {
        $userPrimaryKey = $this->primaryKey;
        return Cache::tags(["user_subscription_" . $this->$userPrimaryKey])->remember("subscription", config()->get('cache.ttl'), function () {
            return $this->subscription;
        });
    }

    public function cachedAPISubscription()
    {
        $userPrimaryKey = $this->primaryKey;
        if (Cache::tags(["user_subscription_" . $this->$userPrimaryKey])->has('api_subscription')) {
            return Cache::tags(["user_subscription_" . $this->$userPrimaryKey])->get('api_subscription');
        } else {
            return Cache::tags(["user_subscription_" . $this->$userPrimaryKey])->remember('api_subscription', config()->get('cache.ttl'), function () {
                $subscriptionRepo = app()->make('App\Contracts\Repository\Subscription\SubscriptionContract');
                if ($this->hasValidSubscription()) {
                    $subscription = $subscriptionRepo->getSubscription($this->cachedSubscription()->api_subscription_id);
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
        $userPrimaryKey = $this->primaryKey;
        Cache::tags(["user_subscription_" . $this->$userPrimaryKey])->flush();
        return $result;
    }

    public static function create(array $attributes = [])
    {
        $user = parent::create($attributes); // TODO: Change the autogenerated stub

        Dashboard::create(array(
            "user_id" => $user->getKey(),
            'dashboard_template' => 1,
            'dashboard_name' => 'Default Dashboard',
            'dashboard_order' => 1
        ));
    }
}

