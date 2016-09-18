<?php
namespace App\Validators\User\Group;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 6:44 PM
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
            'name' => 'required|max:255',
            'url' => 'required|url|max:2083',
            'description' => 'max:255'
        );
    }

    protected function getMessages()
    {
        return array(
            "name.required" => "Group name is required.",
            "name.max" => "Group name accepts maximum 255 characters.",
            "url.required" => "Group URL is required.",
            "url.url" => "Please enter a valid Group URL.",
            "url.max" => "Group URl accepts maximum 2083 characters.",
            "description.max" => "Description accepts maximum 255 characters."
        );
    }
}