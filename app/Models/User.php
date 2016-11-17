<?php

namespace App\Models;

use App\Models\Dashboard\Dashboard;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Chargify;
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
        'title', 'first_name', 'last_name', 'email', 'password', 'verification_code', 'last_login', 'first_login',
        'industry', 'company_type', 'company_name', 'agree_terms'
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
        'preferences', 'apiSubscription', 'apiOnboardingSubscription'
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


    /**
     * attributes
     */

    public function getApiSubscriptionAttribute()
    {
        if (!$this->isStaff() && !is_null($this->subscription) && $this->subscription->isValid()) {
            return Chargify::subscription()->get($this->subscription->api_subscription_id);
        } else {
            return null;
        }
    }

    public function getApiOnboardingSubscriptionAttribute()
    {
        if (!$this->isStaff() && !is_null($this->subscription) && !is_null($this->subscription->api_onboarding_subscription_id)) {
            return Chargify::subscription()->get($this->subscription->api_onboarding_subscription_id);
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

    public function subscriptionCriteria()
    {
        return Cache::remember("user.{$this->getKey()}.subscription.api.criteria", config()->get('cache.ttl'), function () {
            $product = Chargify::product()->get($this->apiSubscription->product_id);
            if (!is_null($product->description)) {
                $criteria = json_decode($product->description);
                return $criteria;
            }
            return null;
        });
    }

    /**
     * Check if the user can still add product
     *
     * @return bool
     */
    public function canAddProduct()
    {
        if ($this->isStaff()) {
            return true;
        }
        $criteria = $this->subscriptionCriteria();
        if (isset($criteria->product) && $criteria->product != 0) {
            $maxProducts = $criteria->product;
            $currentProducts = $this->products()->count();
            if ($currentProducts >= $maxProducts) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the user can still add site
     *
     * @return bool
     */
    public function canAddSite()
    {
        if ($this->isStaff()) {
            return true;
        }
        $criteria = $this->subscriptionCriteria();
        if (isset($criteria->site) && $criteria->site != 0) {
            $maxSites = $criteria->site;
            $currentSites = $this->sites()->count();
            if ($currentSites >= $maxSites) {
                return false;
            }
        }
        return true;
    }

    public function clearCache()
    {
        Cache::forget("user.{$this->getKey()}.subscription.api.criteria");
        Cache::forget("user.{$this->getKey()}.subscription.transaction");
    }

    public function isStaff()
    {
        return $this->hasRole(['super_admin', 'tier_1', 'tier_2']);
    }
}

