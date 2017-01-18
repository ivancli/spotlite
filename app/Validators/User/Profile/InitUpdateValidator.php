<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/28/2016
 * Time: 11:11 AM
 */

namespace App\Validators\User\Profile;


use App\Validators\ValidatorAbstract;

class InitUpdateValidator extends ValidatorAbstract
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
            "industry" => "required|max:255",
            "company_type" => "required|max:255",
            "company_url" => "url|max:2083",
            "category" => "required_if:sample_data,1"
        );
    }
}