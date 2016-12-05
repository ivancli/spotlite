<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/6/2016
 * Time: 9:47 AM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedHistoricalPrice extends Model
{
    protected $primaryKey = "deleted_historical_price_id";
    protected $fillable = [
        "deleted_historical_price_id", "crawler_id", "site_id", "price", "created_at", "updated_at"
    ];
    public $timestamps = false;
}