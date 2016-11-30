<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:14 PM
 */

namespace App\Models;


use App\Models\DeletedRecordModels\DeletedProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $primaryKey = "product_id";
    protected $fillable = [
        "product_name", "category_id", "user_id", "group_id", "product_order", "report_task_id"
    ];
    public $timestamps = false;
    protected $appends = ["urls", "siteCount"];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'category_id');
    }


    public function sites()
    {
        return $this->hasMany('App\Models\Site', 'product_id', 'product_id');
    }

    public function filteredSites()
    {
        if (request()->has('keyword') && !empty(request()->get('keyword'))) {
            $keyword = request()->get('keyword');
            $queryBuilder = $this->hasMany('App\Models\Site', 'product_id', 'product_id');
            $filteredQueryBuilder = $queryBuilder->where('site_url', 'LIKE', "%{$keyword}%");
            $filteredSiteCount = $filteredQueryBuilder->count();
            if ($filteredSiteCount > 0) {
                return $filteredQueryBuilder;
            } else {
                return $queryBuilder;
            }
        } else {
            return $this->hasMany('App\Models\Site', 'product_id', 'product_id');
        }
    }

    public function alert()
    {
        return $this->morphOne('App\Models\Alert', 'alert_owner', null, null, 'product_id');
    }

    public function reportTask()
    {
        return $this->morphOne('App\Models\ReportTask', 'report_task_owner', null, null, 'product_id');
    }

    public function reports()
    {
        return $this->morphMany('App\Models\Report', 'report_owner', null, null, 'product_id');
    }

    public function alertActivityLogs()
    {
        return $this->morphMany('App\Models\Logs\AlertActivityLog', 'alert_activity_log_owner', null, null, 'product_id');
    }

    //indirect relationships

    public function siteAlerts()
    {
        return $this->hasManyThrough('App\Models\Alert', 'App\Models\Site', 'product_id', 'alert_owner_id', 'product_id')->where('alert_owner_type', 'site');
    }

    public function getSiteCountAttribute()
    {
        return $this->sites()->count();
    }

    public function myPriceSite()
    {
        return $this->sites()->where('my_price', 'y')->first();
    }

    public function alertOnMyPrice()
    {
        return $this->alert()->where("comparison_price_type", "my price")->first();
    }

    public function siteAlertsOnMyPrice()
    {
        return $this->siteAlerts()->where("comparison_price_type", "my price")->get();
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("product.show", $this->getKey()),
            "delete" => route("product.destroy", $this->getKey()),
            "alert" => route("alert.product.edit", $this->getKey()),
            "chart" => route("chart.product.index", $this->getKey()),
            "report_task" => route("report_task.product.edit", $this->getKey()),
        );
    }
}