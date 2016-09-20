<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 11:40 AM
 */

namespace App\Models;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ProductSite extends Model
{
    protected $table = "product_sites";
    protected $primaryKey = "product_site_id";
    protected $fillable = ["product_id", "site_id", "my_price"];
    public $appends = ["urls"];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'product_id');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id', 'site_id');
    }

    public function excludingAlerts()
    {
        return $this->belongsToMany('App\Models\Alert', 'alert_excluded_product_sites', 'alert_id', 'product_site_id');
    }

    public function alert()
    {
        return $this->morphOne('App\Models\Alert', 'alert_owner', null, null, 'product_site_id');
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("product_site.show", $this->getKey()),
            "edit" => route("product_site.edit", $this->getKey()),
            "update" => route("product_site.my_price", $this->getKey()),
            "delete" => route("product_site.destroy", $this->getKey()),
            "alert" => route("alert.product_site.edit", $this->getKey()),
            "chart" => route("chart.product_site.index", $this->getKey()),
        );
    }
}