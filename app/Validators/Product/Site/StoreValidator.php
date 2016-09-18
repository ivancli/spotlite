<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:46 PM
 */

namespace App\Validators\Product\Site;


use App\Validators\ValidatorAbstract;

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
            "site_url" => "required|url|max:2083"
        );
    }

    protected function getMessages()
    {
        return array(
            "site_url.required" => "Site URL is required.",
            "site_url.url" => "Please provide a valid URL.",
            "site_url.max" => "Site URL accepts maximum 2083 characters."
        );
    }
}