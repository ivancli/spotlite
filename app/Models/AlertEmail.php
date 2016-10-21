<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 10:09 AM
 */

namespace App\Models;


use App\Models\DeletedRecordModels\DeletedAlertEmail;
use Illuminate\Database\Eloquent\Model;

class AlertEmail extends Model
{
    protected $table = "alert_emails";
    protected $primaryKey = "alert_email_id";
    protected $fillable = ["alert_id", "alert_email_address"];
    public $timestamps = false;

    public function alert()
    {
        return $this->belongsTo('App\Models\Alert', 'alert_id', 'alert_id');
    }
}