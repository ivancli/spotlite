<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:47 PM
 */

namespace App\Validators\Product\Site;


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
            "site_url" => "required"
        );
    }

    protected function getMessages()
    {
        return array(
            "site_url" => "URL of the product page is required."
        );
    }
}