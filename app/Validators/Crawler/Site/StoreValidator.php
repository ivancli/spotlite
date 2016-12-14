<?php
namespace App\Validators\Crawler\Site;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/09/2016
 * Time: 4:37 PM
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
            "site_url" => "required|max:2083|url",
        );
    }

    protected function getMessages()
    {
        return array(
            "site_url.required" => "Please enter: Site URL",
            "site_url.url" => "Please provide a valid URL.",
            "site_url.max" => "Site URL accepts maximum 2083 characters."
        );
    }
}