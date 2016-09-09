<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 11:40 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductSite extends Model
{
    protected $table = "product_sites";
    protected $primaryKey = "product_site_id";
    protected $fillable = ["product_id", "site_id"];
    public $timestamps = false;
    public $appends = ["urls"];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'product_id');
    }

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id', 'site_id');
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("product_site.show", $this->getKey()),
            "edit" => route("product_site.edit", $this->getKey()),
            "delete" => route("product_site.destroy", $this->getKey()),
        );
    }
}