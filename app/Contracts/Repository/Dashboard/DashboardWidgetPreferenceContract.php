<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/14/2016
 * Time: 11:45 AM
 */

namespace App\Contracts\Repository\Dashboard;


interface DashboardWidgetPreferenceContract
{
    public function getPreferencesByDashboardWidget($dashboard_widget_id);

    public function getPreference($dashboard_widget_preference_id);

    public function getPreferenceByNameAndDashboardWidget($dashboard_widget_id, $preference_name);

    public function getPreferences();

    public function storePreference($options);

    public function updatePreference($options, $dashboard_widget_preference_id);
}