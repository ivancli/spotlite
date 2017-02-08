<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:08 PM
 */

namespace App\Models;


use App\Filters\QueryFilter;
use App\Models\DeletedRecordModels\DeletedSite;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $primaryKey = "site_id";
    protected $fillable = [
        "product_id", "site_url", "recent_price", "last_crawled_at", "price_diff", "status", "my_price", "comment", "site_order", "created_at", "updated_at"
    ];
    protected $appends = ['urls', 'domain', 'previousPrice', 'diffPrice', 'priceLastChangedAt'];

    public function preference()
    {
        return $this->hasOne('App\Models\SitePreference', 'site_id', 'site_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'product_id');
    }

    public function crawler()
    {
        return $this->hasOne('App\Models\Crawler', 'site_id', 'site_id');
    }

    public function alert()
    {
        return $this->morphOne('App\Models\Alert', 'alert_owner', null, null, 'site_id');
    }

    public function alertActivityLogs()
    {
        return $this->morphMany('App\Models\Logs\AlertActivityLog', 'alert_activity_log_owner', null, null, 'site_id');
    }

    public function excludedByAlerts()
    {
        return $this->belongsToMany('App\Models\Alert', 'alert_exclude_sites', 'site_id', 'alert_id');
    }

    public function historicalPrices()
    {
        return $this->hasMany('App\Models\HistoricalPrice', 'site_id', 'site_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUrlsAttribute()
    {
        return array(
            "admin_update" => route("admin.site.update", $this->getKey()),
            "test" => route("admin.site.test", $this->getKey()),
            "admin_delete" => route("admin.site.destroy", $this->getKey()),
            "admin_crawler_edit" => !is_null($this->crawler) ? route("admin.crawler.edit", $this->crawler->getKey()) : null,
            "show" => route("site.show", $this->getKey()),

            "edit" => route("site.edit", $this->getKey()),
            "update" => route("site.update", $this->getKey()),
            "update_my_price" => route("site.my_price", $this->getKey()),
            "delete" => route("site.destroy", $this->getKey()),

            "alert" => route("alert.site.edit", $this->getKey()),

            "chart" => route("chart.site.index", $this->getKey()),

            "admin_xpath_edit" => route("admin.site.xpath.edit", $this->getKey()),

            "admin_status_update" => route("admin.site.status.update", $this->getKey()),
        );
    }

    public function getPreviousPriceAttribute()
    {
        $builder = $this->historicalPrices()->orderBy('created_at', 'desc')->where('price', '!=', $this->recent_price);
        if (isset($this->product) && !is_null($this->product)) {
            $user = $this->product->user;
            if ($user->needSubscription && !is_null($user->subscription) && $user->subscriptionCriteria()->historic_pricing > 0) {
                $limitedDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-{$user->subscriptionCriteria()->historic_pricing} month"));
                $builder->where('created_at', '>=', $limitedDate);
            }
            return $builder->first();
        }
        return null;
    }

    public function getDiffPriceAttribute()
    {
        $historicalPrice = $this->previousPrice;
        if (!is_null($historicalPrice) && !is_null($this->recent_price)) {
            return $this->recent_price - $historicalPrice->price;
        }
        return null;
    }

    public function getPriceLastChangedAtAttribute()
    {
        $historicalPrice = $this->previousPrice;
        if (!is_null($historicalPrice)) {
            $firstChangedHistoricalPrice = $this->historicalPrices()->orderBy('created_at', 'asc')->where('price', $this->recent_price)->where('price_id', '>', $historicalPrice->getKey())->first();
            if (!is_null($firstChangedHistoricalPrice)) {
                return $firstChangedHistoricalPrice->created_at;
            }
        }
        return null;
    }

    public function getDomainAttribute()
    {
        return parse_url($this->site_url)['host'];
    }

    public function statusOK()
    {
        $this->status = "ok";
        $this->save();
    }

    public function statusNoPrice()
    {
        $this->status = "no_price";
        $this->save();
    }

    public function statusFailHtml()
    {
        $this->status = "fail_html";
        $this->save();
    }

    public function statusFailPrice()
    {
        $this->status = "fail_price";
        $this->save();
    }

    public function statusFailXpath()
    {
        $this->status = "fail_xpath";
        $this->save();
    }

    public function statusNullXpath()
    {
        $this->status = "null_xpath";
        $this->save();
    }

    public function statusWaiting()
    {
        $this->status = "waiting";
        $this->save();
    }

    public function statusInvalid()
    {
        $this->status = "invalid";
        $this->save();
    }

    public function lastCrawledWithinHour($hour = 1)
    {
        if(!is_null($this->last_crawled_at)){
            return false;
        }
        $hourDiff = (strtotime(date('Y-m-d H:00:00')) - strtotime(date('Y-m-d H:00:00', strtotime($this->last_crawled_at)))) / 3600;
        return $hourDiff < $hour;
    }
}