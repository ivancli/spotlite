<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/18/2017
 * Time: 11:26 AM
 */

namespace App\Validators\Product\Alert;


use App\Validators\ValidatorAbstract;

class UpdateAlertValidator extends ValidatorAbstract
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
            "products.*.specificPrice" => "required_if:products.*.type,=<",
        );
    }
}