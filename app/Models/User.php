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
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = "user_id";
    protected $fillable = [
        'title', 'first_name', 'last_name', 'email', 'password', 'verification_code', 'last_login', 'first_login', 'subscription_location',
        'industry', 'company_type', 'company_name', 'company_url', 'ebay_username', 'agree_terms', 'set_password', 'conversion_tracked'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_code'
    ];

    protected $appends = [
        'preferences', 'apiSubscription', 'isStaff', 'isUnlimitedClient', 'firstAvailableDashboard', 'needSubscription', 'numberOfLogin', 'isPastDue', 'isCancelled'
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

    public function productMetas()
    {
        return $this->hasManyThrough('App\Models\ProductMeta', 'App\Models\Product', 'user_id', 'product_id', 'user_id');
    }

    public function dashboards()
    {
        return $this->hasMany('App\Models\Dashboard\Dashboard', 'user_id', 'user_id')->orderBy("dashboard_order", "asc");
    }

    public function alerts()
    {
        return $this->morphMany('App\Models\Alert', 'alert_owner', null, null, 'user_id');
    }

    public function domains()
    {
        return $this->hasMany('App\Models\UserDomain', 'user_id', 'user_id')->orderBy('domain', 'asc');
    }

    public function reportTask()
    {
        return $this->morphOne('App\Models\ReportTask', 'report_task_owner', null, null, 'user_id');
    }

    public function nonHiddenDashboards()
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

    public function categoryReports()
    {
        return $this->hasManyThrough('App\Models\Report', 'App\Models\Category', 'user_id', 'report_owner_id', 'user_id')->where('report_owner_type', 'category');
    }

    public function productReports()
    {
        return $this->hasManyThrough('App\Models\Report', 'App\Models\Product', 'user_id', 'report_owner_id', 'user_id')->where('report_owner_type', 'product');
    }

    public function categoryAlerts()
    {
        return $this->hasManyThrough('App\Models\Alert', 'App\Models\Category', 'user_id', 'alert_owner_id', 'user_id')->where('alert_owner_type', 'category');
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
        if ($this->needSubscription && !is_null($this->subscription)) {
            $subscription = $this->subscription;
            return Cache::tags(['users', "user_" . $this->getKey()])->remember('api_subscription', config('cache.ttl'), function ()  use($subscription){
                return Chargify::subscription($subscription->subscription_location)->get($subscription->api_subscription_id);
            });
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

    public function getIsStaffAttribute()
    {
        return $this->hasRole(['super_admin', 'tier_1', 'tier_2', 'crawler_maintainer']);
    }

    public function getIsUnlimitedClientAttribute()
    {
        return $this->hasRole(['unlimited_client']);
    }

    public function getFirstAvailableDashboardAttribute()
    {
        $dashboard = $this->dashboards()->orderBy('dashboard_order', 'asc')->first();
        return $dashboard;
    }

    public function getNeedSubscriptionAttribute()
    {
        return !$this->isStaff && !$this->isUnlimitedClient;
    }

    public function getNumberOfLoginAttribute()
    {
        return $this->activityLogs()->where('activity', 'login')->count();
    }

    public function getIsPastDueAttribute()
    {
        return !is_null($this->subscription) && $this->subscription->isPastDue;
    }

    public function getIsCancelledAttribute()
    {
        return !is_null($this->subscription) && $this->subscription->isCancelled;
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
        if (!is_null($this->apiSubscription)) {
            return Cache::tags(['users', "user_" . $this->getKey()])->remember('subscription_criteria', config('cache.ttl'), function () {
                $product = Chargify::product($this->subscription->subscription_location)->get($this->apiSubscription->product_id);
                if (!is_null($product->description)) {
                    $criteria = json_decode($product->description);
                    return $criteria;
                }
            });
        }
        return null;
    }

    public function setConversionTracked()
    {
        $this->conversion_tracked = 'y';
        $this->save();
    }

    /**
     * Check if the user can still add product
     *
     * @return bool
     */
    public function canAddProduct()
    {
        if (!$this->needSubscription) {
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
    public function canAddSite($productId)
    {
        if (!$this->needSubscription) {
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

    public function clearAllCache()
    {
        $this->clearCache();
        Cache::tags(["user_{$this->getKey()}"])->flush();
        Cache::tags(["user_" . $this->getKey()])->flush();
        Cache::tags(['users'])->flush();
        if (!is_null($this->apiSubscription)) {
            Cache::tags([$this->subscription_location . "subscriptions.{$this->apiSubscription->id}"])->flush();
            Cache::tags([$this->subscription_location . 'chargify', $this->subscription_location . 'subscriptions', $this->subscription_location . 'subscription', $this->subscription_location . "subscriptions.{$this->apiSubscription->id}"])->flush();
        }
    }
}

