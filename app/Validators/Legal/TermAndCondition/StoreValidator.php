<?php
namespace App\Validators\Legal\TermAndCondition;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/17/2017
 * Time: 3:48 PM
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
            "dashboard_widget_type_id" => "required|exists:dashboard_widget_types,dashboard_widget_type_id",
            "dashboard_widget_name" => "required|max:255"
        );
    }

    protected function getMessages()
    {
        return array(
            "dashboard_id.required" => "Dashboard is required.",
            "dashboard_widget_type_id.required" => "Chart type is required.",
            "dashboard_widget_name.required" => "Chart name is required.",
            "dashboard_widget_name.max" => "Chart name accept maximum 255 characters.",
        );
    }
}