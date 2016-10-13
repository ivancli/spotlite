<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:18 AM
 */

namespace App\Models\Dashboard;


use Illuminate\Database\Eloquent\Model;

class DashboardTemplate extends Model
{
    protected $primaryKey = "dashboard_template_id";
    protected $fillable = [
        "dashboard_template_name", "dashboard_template_display_name", "is_hidden"
    ];
    public $timestamps = false;

    public function dashboards()
    {
        return $this->hasMany('App\Models\Dashboard\Dashboard', 'dashboard_template_id', 'dashboard_template_id');
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