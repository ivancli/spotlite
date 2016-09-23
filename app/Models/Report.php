<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 4:21 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = "reports";
    protected $primaryKey = "report_id";
    protected $fillable = ["report_task_id", "content"];

    public function reportTask()
    {
        return $this->belongsTo('App\Models\ReportTask', 'report_task_id', 'report_task_id');
    }
}