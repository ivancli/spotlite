<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/22/2016
 * Time: 5:29 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportTask extends Model
{
    protected $primaryKey = "report_task_id";
    protected $fillable = [
        "report_task_owner_type", "report_task_owner_id", "frequency", "date", "day", "time", "weekday_only", "delivery_method", "status"
    ];
    public $timestamps = false;

    public function reportable()
    {
        return $this->morphTo("report_task_owner", "report_task_owner_type", "report_task_owner_id");
    }

    public function emails()
    {
        return $this->hasMany('App\Models\ReportEmail', 'report_task_id', 'report_task_id');
    }
}