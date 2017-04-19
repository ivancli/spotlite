<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/11/2017
 * Time: 9:41 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EbayItem extends Model
{
    protected $primaryKey = "ebay_item_id";
    protected $fillable = ['title', 'subtitle', 'shortDescription', 'price', 'currency', 'category', 'condition', 'location_city', 'location_postcode', 'location_country', 'image_url', 'brand', 'seller_username',];
    protected $appends = [];


    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id', 'site_id');
    }
}