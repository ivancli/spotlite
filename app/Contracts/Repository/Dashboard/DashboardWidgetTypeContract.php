<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 5:28 PM
 */

namespace App\Contracts\Repository\Dashboard;


interface DashboardWidgetTypeContract
{
    public function getDashboardWidgetTypes();

    public function getDashboardWidgetType($id, $fail = true);

    public function storeDashboardWidgetType($options);

    public function updateDashboardWidgetType($options, $id);

    public function deleteDashboardWidgetType($id);
}