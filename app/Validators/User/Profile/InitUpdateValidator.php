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
            "company_url" => "url",
            "category" => "required_if:sample_data,1"
        );
    }

    protected function getMessages()
    {
        return array(
            "industry.required" => "Industry is required.",
            "company_type.required" => "Company type is required.",
            "company_url.url" => "Please enter a correct URL for your site (including the http://).",
            "category.required_if" => "Please select sample products from the list."
        );
    }
}