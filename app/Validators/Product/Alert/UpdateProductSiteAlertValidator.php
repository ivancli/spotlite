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
            "email" => "required|array"
        );
    }
}