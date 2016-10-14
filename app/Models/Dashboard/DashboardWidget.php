<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:22 AM
 */

namespace App\Models\Dashboard;


use App\Contracts\Repository\Product\Site\SiteContract;
use App\Models\Category;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    protected $primaryKey = "dashboard_widget_id";
    protected $fillable = [
        "dashboard_id", "dashboard_widget_type_id", "dashboard_widget_name", "dashboard_widget_order"
    ];
    public $timestamps = false;
    protected $appends = ['urls'];

    public function dashboard()
    {
        return $this->belongsTo('App\Models\Dashboard\Dashboard', 'dashboard_id', 'dashboard_id');
    }

    public function widgetType()
    {
        return $this->belongsTo('App\Models\Dashboard\DashboardWidgetType', 'dashboard_widget_type_id', 'dashboard_widget_type_id');
    }

    public function preferences()
    {
        return $this->hasMany('App\Models\Dashboard\DashboardWidgetPreference', 'dashboard_widget_id', 'dashboard_widget_id');
    }

    public function getPreference($dashboard_widget_preference_element)
    {
        $preference = $this->preferences()->where('element', $dashboard_widget_preference_element)->first();
        return is_null($preference) ? null : $preference->value;
    }

    public function setPreference($dashboard_widget_preference_element, $dashboard_widget_preference_value)
    {
        $pref = $this->getPreference($this->getKey(), $dashboard_widget_preference_element);
        if (!is_null($pref)) {
            $pref->value = $dashboard_widget_preference_value;
            $pref->save();
            return $pref;
        } else {
            $pref = DashboardWidgetPreference::create(array(
                "dashboard_widget_id" => $this->getKey(),
                "element" => $dashboard_widget_preference_element,
                "value" => $dashboard_widget_preference_value
            ));
        }
        return $pref;
    }

    public function clearPreferences()
    {
        $preferences = $this->preferences;
        foreach ($preferences as $pref) {
            $pref->delete();
        }
    }

    public function site()
    {
        if (!is_null($this->getPreference('site_id'))) {
            $site = Site::findOrFail($this->getPreference('site_id'));
            return $site;
        } else {
            return null;
        }
    }

    public function product()
    {
        if (!is_null($this->getPreference('product_id'))) {
            $product = Product::findOrFail($this->getPreference('product_id'));
            return $product;
        } else {
            return null;
        }
    }

    public function category()
    {
        if (!is_null($this->getPreference('category_id'))) {
            $category = Category::findOrFail($this->getPreference('category_id'));
            return $category;
        } else {
            return null;
        }
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("dashboard.widget.show", $this->getKey()),
            "delete" => route("dashboard.widget.destroy", $this->getKey()),
        );
    }
}