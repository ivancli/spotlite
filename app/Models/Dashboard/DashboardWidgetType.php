<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:23 AM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardWidgetType extends Model
{
    protected $primaryKey = "dashboard_widget_type_id";
    protected $fillable = [
        "dashboard_widget_template_id", "dashboard_widget_type_name"
    ];
    public $timestamps = false;

    public function widgets()
    {
        return $this->hasMany('App\Models\Dashboard\DashboardWidget', 'dashboard_widget_type_id', 'dashboard_widget_type_id');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardWidgetTemplate', 'dashboard_widget_template_id', 'dashboard_widget_template_id');
    }
}