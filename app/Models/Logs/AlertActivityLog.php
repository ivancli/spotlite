<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 10:35 AM
 */

namespace App\Models\Logs;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class AlertActivityLog extends Model
{
    protected $primaryKey = "alert_activity_log_id";
    protected $fillable = [
        'alert_id', 'type', 'content',
    ];

    public function alert()
    {
        return $this->belongsTo('App\Models\Alert', 'alert_id', 'alert_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}