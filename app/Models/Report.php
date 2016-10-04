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
    protected $fillable = ["report_task_id", "report_owner_type", "report_owner_id", "content", "file_name", "file_type"];
    protected $appends = ["urls"];

    public function reportable()
    {
        return $this->morphTo("report_owner", "report_owner_type", "report_owner_id");
    }

    public function reportTask()
    {
        return $this->belongsTo('App\Models\ReportTask', 'report_task_id', 'report_task_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("report.show", $this->getKey()),
            "delete" => route("report.destroy", $this->getKey())
        );
    }

}