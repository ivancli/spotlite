<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 9/09/2016
 * Time: 10:48 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedAlertEmail extends Model
{
    protected $primaryKey = "deleted_alert_email_id";
    protected $fillable = ["content"];
}