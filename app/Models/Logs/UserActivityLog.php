<?php
namespace App\Models\Logs;

use App\Filters\QueryFilter;
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

    protected $appends = array('urls');

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUrlsAttribute()
    {
        return array(
            "owner" => route("log.user_activity.show", $this->owner->getKey()),
        );
    }
}