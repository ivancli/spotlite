<?php
namespace App\Validators\Dashboard\DashboardWidget;

use App\Exceptions\ValidationException;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 12:06 PM
 */
class StoreValidator extends ValidatorAbstract
{
    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return array(
            "dashboard_id" => "required|exists:dashboards,dashboard_id",
            "dashboard_widget_type_id" => "required|exists:dashboard_widget_types,dashboard_widget_type_id",
            "dashboard_widget_name" => "required|max:255"
        );
    }

    protected function getMessages()
    {
        return array(
            "dashboard_id.required" => "Dashboard is required.",
            "dashboard_widget_type_id.required" => "Content type is required.",
            "dashboard_widget_name.required" => "Content name is required.",
            "dashboard_widget_name.max" => "Content name accept maximum 255 characters.",
        );
    }
}