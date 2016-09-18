<?php
namespace App\Validators\UM\Group;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:55 PM
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
            'name' => 'required|unique:groups|max:255|min:1',
            'active' => 'boolean',
            'url' => 'required|url|max:2083|min:1',
            'description' => 'max:255'
        );
    }

    protected function getMessages()
    {
        return array(
            "name.required" => "Group name is required.",
            "name.unique" => "Group name already exists.",
            "name.max" => "Group name accepts maximum 255 characters.",
            "name.min" => "Group name is required.",
            "active.boolean" => "Is group active?",
            "url.required" => "Group URL is required.",
            "url.url" => "Please provide a valid Group URL.",
            "url.max" => "Group URL accepts maximum 2083 characters.",
            "url.min" => "Group URL is required.",
            "description" => "Description accepts maximum 255 characters."
        );
    }
}