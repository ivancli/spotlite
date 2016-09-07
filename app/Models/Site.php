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

    /**
     * back up category before deleting
     * @return bool|null
     */
    public function delete()
    {
        DeletedSite::create(array(
            "content" => $this->toJson()
        ));
        return parent::delete(); // TODO: Change the autogenerated stub
    }


    public function getUrlsAttribute()
    {
        return array(
            "show" => route("site.show", $this->getKey()),
            "edit" => route("site.edit", $this->getKey()),
            "delete" => route("site.destroy", $this->getKey()),
        );
    }
}