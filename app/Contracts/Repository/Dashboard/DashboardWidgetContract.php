<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 4:34 PM
 */

namespace App\Contracts\Repository\Dashboard;


interface DashboardWidgetContract
{
    public function getWidget($id, $fail = true);

    public function getWidgets();

    public function getWidgetData($id);

    public function storeWidget($options);

    public function updateWidget($options, $id);

    public function deleteWidget($id);
}