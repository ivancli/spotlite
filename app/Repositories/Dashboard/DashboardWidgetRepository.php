<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 4:36 PM
 */

namespace App\Repositories\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardWidgetContract;
use App\Models\Dashboard\DashboardWidget;

class DashboardWidgetRepository implements DashboardWidgetContract
{

    public function getWidget($id, $fail = true)
    {
        if ($fail == true) {
            return DashboardWidget::findOrFail($id);
        } else {
            return DashboardWidget::find($id);
        }
    }

    public function getWidgets()
    {
        return DashboardWidget::all();
    }

    public function storeWidget($options)
    {
        DashboardWidget::create($options);
    }

    public function updateWidget($options, $id)
    {
        $widget = $this->getWidget($id);
        $widget->update($options);
        return $widget;
    }

    public function deleteWidget($id)
    {
        $widget = $this->getWidget($id);
        $widget->delete();
        return true;
    }
}