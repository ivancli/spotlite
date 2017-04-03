<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/22/2016
 * Time: 5:29 PM
 */

namespace App\Models;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    public $timestamps = false;
    protected $primaryKey = "report_task_id";
    protected $fillable = [
        "report_task_owner_type", "report_task_owner_id", "frequency", "date", "day", "time", "weekday_only", "delivery_method", "file_type", "status", "last_sent_at"
    ];
    protected $appends = ["urls"];

    public function reportable()
    {
        return $this->morphTo("report_task_owner", "report_task_owner_type", "report_task_owner_id");
    }

    public function emails()
    {
        return $this->hasMany('App\Models\ReportEmail', 'report_task_id', 'report_task_id');
    }

    public function setLastSentStamp()
    {
        $this->last_sent_at = date("Y-m-d H:i:s");
        $this->save();
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }


    public function getUrlsAttribute()
    {
        $reportTaskOwnerType = $this->report_task_owner_type;
        $reportTaskOwnerId = $this->report_task_owner_id;
        if ($reportTaskOwnerType != 'user') {
            return array(
                "edit" => route("report_task.$reportTaskOwnerType.edit", $reportTaskOwnerId),
                "delete" => route("report_task.$reportTaskOwnerType.destroy", $reportTaskOwnerId),
            );
        } else {
            return [];
        }
    }

}