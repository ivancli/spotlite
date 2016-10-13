<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:13 AM
 */

namespace App\Models\Dashboard;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $primaryKey = "dashboard_id";
    protected $fillable = [
        "user_id", "dashboard_template_id", "dashboard_name", "dashboard_order", "is_hidden"
    ];
    public $timestamps = false;
    protected $appends = ["urls"];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function widgets()
    {
        return $this->hasMany('App\Models\Dashboard\DashboardWidget', 'dashboard_id', 'dashboard_id');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardTemplate', 'dashboard_template_id', 'dashboard_template_id');
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

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUrlsAttribute()
    {
        return array(
            "edit" => route("dashboard.edit", $this->getKey()),
            "show" => route("dashboard.show", $this->getKey()),
            "delete" => route("dashboard.destroy", $this->getKey())
        );
    }

}