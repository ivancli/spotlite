<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:22 AM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    protected $primaryKey = "dashboard_widget_id";
    protected $fillable = [
        "dashboard_id", "dashboard_widget_type_id", "dashboard_widget_name", "dashboard_widget_order"
    ];
    public $timestamps = false;

    public function dashboard()
    {
        return $this->belongsTo('App\Models\Dashboard\Dashboard', 'dashboard_id', 'dashboard_id');
    }

    public function widgetType()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardWidgetType', 'dashboard_widget_type_id', 'dashboard_widget_type_id');
    }
}