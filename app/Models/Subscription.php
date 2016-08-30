<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 5:09 PM
 */
class Subscription extends Model
{
    protected $primaryKey = "subscription_id";
    protected $fillable = [
        'api_product_id', 'api_custom_id', 'api_subscription_id', 'expiry_date', 'cancelled_at',
    ];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'user_id');
    }

    public function isValid()
    {
        if (!is_null($this->expiry_date)) {
            return strtotime($this->expiry_date) > time();
        } else {
            return is_null($this->cancelled_at) || $this->cancelled_at > time();
        }

    }
}