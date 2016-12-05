<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:08 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedSite extends Model
{
    protected $primaryKey = "deleted_site_id";
    protected $fillable = ['deleted_site_id', 'created_at', 'updated_at', 'site_url', 'my_price', 'status', 'recent_price', 'price_diff', 'last_crawled_at', 'comment', 'site_order'];
    public $timestamps = false;
}