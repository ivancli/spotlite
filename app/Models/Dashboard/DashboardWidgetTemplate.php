<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:33 AM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardWidgetTemplate extends Model
{
    protected $primaryKey = "dashboard_widget_template_id";
    protected $fillable = [
        "dashboard_widget_template_name", "dashboard_widget_template_display_name", "is_hidden"
    ];
    public $timestamps = false;

    public function widgets()
    {
        return $this->hasMany('App\Models\Dashboard\DashboardWidgetType', 'dashboard_widget_template_id', 'dashboard_widget_template_id');
    }

    public function hide()
    {
        $this->is_hidden = 'y';
        $this->save();
    }

    public function show()
    {
        $this->is_hidden = 'n';
        $this->save();
    }
}