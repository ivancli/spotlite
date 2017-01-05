<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 1:33 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class HistoricalPrice extends Model
{
    protected $primaryKey = "price_id";
    protected $fillable = [
        "crawler_id", "site_id", "price", "created_at", "updated_at"
    ];
}