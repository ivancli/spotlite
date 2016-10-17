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
        return $this->hasMany('App\Models\Dashboard\DashboardWidget', 'dashboard_id', 'dashboard_id')->orderBy('dashboard_widget_order', 'asc')->orderBy('dashboard_widget_id', 'asc');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardTemplate', 'dashboard_template_id', 'dashboard_template_id');
    }

    public function clearPreferences()
    {
        $preferences = $this->preferences;
        foreach ($preferences as $pref) {
            $pref->delete();
        }
    }

    public function preferences()
    {
        return $this->hasMany('App\Models\Dashboard\DashboardPreference', 'dashboard_id', 'dashboard_id');
    }

    public function preference($dashboard_preference_element){
        return $this->hasMany('App\Models\Dashboard\DashboardPreference', 'dashboard_id', 'dashboard_id')->where('element', $dashboard_preference_element)->first();
    }

    public function getPreference($dashboard_preference_element)
    {
        $preference = $this->preference($dashboard_preference_element);
        return is_null($preference) ? null : $preference->value;
    }

    public function setPreference($dashboard_preference_element, $dashboard_preference_value)
    {
        $pref = $this->preference($dashboard_preference_element);
        if (!is_null($pref)) {
            $pref->value = $dashboard_preference_value;
            $pref->save();
            return $pref;
        } else {
            $pref = DashboardPreference::create(array(
                "dashboard_id" => $this->getKey(),
                "element" => $dashboard_preference_element,
                "value" => $dashboard_preference_value
            ));
        }
        return $pref;
    }

    public function deletePreference($dashboard_preference_element)
    {
        $preference = $this->preferences()->where('element', $dashboard_preference_element)->first();
        $preference->delete();
        return true;
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