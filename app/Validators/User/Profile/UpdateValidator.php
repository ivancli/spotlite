<?php
namespace App\Validators\User\Profile;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 2:24 PM
 */
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
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "industry" => "required",
            "company_type" => "required",
//            "company_name" => "required|max:255",
        );
    }

    protected function getMessages()
    {
        return array(
            "first_name.required" => "First name is required.",
            "first_name.max" => "First name accepts maximum 255 characters.",
            "last_name.required" => "Last name is required.",
            "last_name.max" => "Last name accepts maximum 255 characters.",
            "industry.required" => "Industry is required.",
            "company_type.required" => "Company type is required.",
//            "company_name.required" => "Company name is required.",
//            "company_name.max" => "Company name accepts maximum 255 characters.",
        );
    }
}