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
        "site_url", "site_xpath", "recent_price", "last_crawled_at"
    ];
    protected $appends = ['urls', 'domain'];

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

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
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

        /* create one-to-one crawler when site is created */
        Crawler::create(array(
            "site_id" => $site->getKey()
        ));

        /* create domain if the domain of site url does not exist */
        $newDomain = parse_url($site->site_url)['host'];

        if (Domain::where('domain_url', $newDomain)->count() == 0) {
            Domain::create(array(
                "domain_url" => $newDomain
            ));
        }

        return $site;
    }


    public function getUrlsAttribute()
    {
        return array(
            "admin_update" => route("admin.site.update", $this->getKey()),
            "test" => route("admin.site.test", $this->getKey()),
            "admin_delete" => route("admin.site.destroy", $this->getKey()),
        );
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
}