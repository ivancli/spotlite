<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 10:19 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ReportEmail extends Model
{
    protected $table = "report_emails";
    protected $primaryKey = "report_email_id";
    protected $fillable = ["report_task_id", "report_email_address"];
    public $timestamps = false;

    public function reportTask()
    {
        return $this->belongsTo('App\Models\ReportTask', 'report_task_id', 'report_task_id');
    }
}