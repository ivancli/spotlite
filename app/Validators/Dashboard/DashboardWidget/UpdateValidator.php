<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 17/10/2016
 * Time: 12:09 PM
 */

namespace App\Validators\Dashboard\DashboardWidget;


use App\Validators\ValidatorAbstract;

class UpdateValidator extends ValidatorAbstract
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
            "dashboard_widget_type_id" => "required|exists:dashboard_widget_types,dashboard_widget_type_id",
            "dashboard_widget_name" => "required|max:255"
        );
    }

    protected function getMessages()
    {
        return array(
            "dashboard_widget_type_id.required" => "Content type is required.",
            "dashboard_widget_name.required" => "Content name is required.",
            "dashboard_widget_name.max" => "Content name accept maximum 255 characters.",
        );
    }
}