<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:08 PM
 */

namespace App\Models;


use App\Filters\QueryFilter;
use App\Models\DeletedRecordModels\DeletedCategory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = "category_id";
    protected $fillable = [
        "category_name", "user_id", "category_order", "report_task_id"
    ];
    protected $appends = ["urls", "productCount", "siteCount"];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'category_id', 'category_id');
    }

    /*filtered products*/
    public function filteredProducts()
    {
        if (request()->has('keyword') && !empty(request()->get('keyword'))) {
            $keyword = request()->get('keyword');
            $queryBuilder = $this->products();
            $filteredQueryBuilder = $queryBuilder->where(function ($query) use ($keyword) {
                $query->where('product_name', 'LIKE', "%{$keyword}%")->orWhereHas('sites', function ($query) use ($keyword) {
                    $query->where('site_url', 'LIKE', "%{$keyword}%");
                });
            });
            $filteredProductCount = $filteredQueryBuilder->count();
            if ($filteredProductCount > 0) {
                return $filteredQueryBuilder;
            } else {
                return $queryBuilder;
            }
        } else {
            return $this->hasMany('App\Models\Product', 'category_id', 'category_id');
        }
    }

    public function sites()
    {
        return $this->hasManyThrough('App\Models\Site', 'App\Models\Product', 'category_id', 'product_id', 'category_id');
    }

    public function alert()
    {
        return $this->morphOne('App\Models\Alert', 'alert_owner', null, null, 'category_id');
    }

    public function alertActivityLogs()
    {
        return $this->morphMany('App\Models\Logs\AlertActivityLog', 'alert_activity_log_owner', null, null, 'category_id');
    }

    public function productAlerts()
    {
        return $this->hasManyThrough('App\Models\Alert', 'App\Models\Product', 'category_id', 'alert_owner_id', 'category_id')->where("alert_owner_type", "product");
    }

    public function reportTask()
    {
        return $this->morphOne('App\Models\ReportTask', 'report_task_owner', null, null, 'category_id');
    }

    public function reports()
    {
        return $this->morphMany('App\Models\Report', 'report_owner', null, null, 'category_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    public function getSiteCountAttribute()
    {
        return $this->sites()->count();
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("category.show", $this->getKey()),
            "delete" => route("category.destroy", $this->getKey()),
            "chart" => route("chart.category.index", $this->getKey()),
            "report_task" => route("report_task.category.edit", $this->getKey()),
            "site_usage" => route('category.site.usage', $this->getKey()),
            "show_products" => route('product.category.products', $this->getKey()),
        );
    }
}