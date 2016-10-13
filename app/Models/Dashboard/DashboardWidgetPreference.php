<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:28 AM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardWidgetPreference extends Model
{
    protected $primaryKey = "dashboard_widget_preference_id";
    protected $fillable = [
        "dashboard_widget_id", "element", "value"
    ];
    public $timestamps = false;

    public function widget()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardWidget', 'dashboard_widget_id', 'dashboard_widget_id');
    }
}