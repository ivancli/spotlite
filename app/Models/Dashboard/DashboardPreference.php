<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 17/10/2016
 * Time: 2:40 PM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardPreference extends Model
{
    protected $primaryKey = "dashboard_preference_id";
    protected $fillable = [
        "dashboard_id", "element", "value"
    ];
    public $timestamps = false;

    public function dashboard()
    {
        return $this->belongsTo('App\Models\Dashboard\Dashboard', 'dashboard_id', 'dashboard_id');
    }
}