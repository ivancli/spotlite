<?php
namespace App\Validators\Product\Alert;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 6:56 PM
 */
class UpdateProductSiteAlertValidator extends ValidatorAbstract
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
            "comparison_price_type" => "required",
            "operator" => "required",
            "comparison_price" => "required_if:comparison_price_type,specific price|numeric",
            "email" => "required|array",
            "email.*" => "email",
        );
    }

    protected function getMessages()
    {
        return array(
            "comparison_price_type.required" => "Trigger is required.",
            "operator.required" => "Trend is required.",
            "comparison_price.required_if" => "Price point is required.",
            "comparison_price.numeric" => "Price point is not in a correct format.",
            "email.required" => "Please provide at least 1 email address to be notified.",
            "email.*.email" => "Please enter a valid email address."
        );
    }
}