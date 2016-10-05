<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 1:15 PM
 */

namespace App\Models\Logs;


use Illuminate\Database\Eloquent\Model;

class ReportActivityLog extends Model
{
    protected $primaryKey = "report_activity_log_id";
    protected $fillable = [
        'report_task_id', 'type', 'content',
    ];

    public function reportTask()
    {
        return $this->belongsTo('App\Models\ReportTask', 'report_task_id', 'report_task_id');
    }
}