<?php
namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 11:48 AM
 */
class UserActivityLog extends Model
{
    protected $primaryKey = "user_activity_log_id";
    protected $fillable = [
        'user_id', 'activity',
    ];

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}