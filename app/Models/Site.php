<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:08 PM
 */

namespace App\Models;


use App\Models\DeletedRecordModels\DeletedSite;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $primaryKey = "site_id";
    protected $fillable = [
        "site_url", "recent_price", "last_crawled_at"
    ];
    protected $appends = ['urls'];

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'product_sites', 'site_id', 'product_id')->withPivot('product_site_id');
    }

    public function crawler()
    {
        return $this->hasOne('App\Models\Crawler', 'site_id', 'site_id');
    }

    public function productSite()
    {
        return $this->hasMany('App\Models\ProductSite', 'site_id', 'site_id');
    }

    public function alerts()
    {
        return $this->morphMany('App\Models\Alert', 'alert_owner', 'alert_owner_type', 'alert_owner_id', 'site_id');
    }

    public function excludedByAlerts()
    {
        return $this->belongsToMany('App\Models\Alert', 'alert_exclude_sites', 'site_id', 'alert_id');
    }

    /**
     * back up category before deleting
     * @return bool|null
     */
    public function delete()
    {
        DeletedSite::create(array(
            "content" => $this->toJson()
        ));
        return parent::delete();
    }

    public static function create(array $attributes = [])
    {
        $site = parent::create($attributes);
        Crawler::create(array(
            "site_id" => $site->getKey()
        ));
        return $site;
    }


    public function getUrlsAttribute()
    {
        return array(
//            "show" => route("site.show", $this->getKey()),
//            "edit" => route("site.edit", $this->getKey()),
//            "delete" => route("site.destroy", $this->getKey()),
        );
    }
}