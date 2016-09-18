<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:58 PM
 */

namespace App\Validators\UM\Group;


use App\Exceptions\ValidationException;
use App\Validators\ValidatorAbstract;

class UpdateValidator extends ValidatorAbstract
{
    public function validate(array $data, $throw = true)
    {
        $rules = $this->getRules(isset($data['id']) ? $data['id'] : null);
        $validation = $this->validator->make($data, $rules);
        if ($validation->fails()) {
            if ($throw) {
                throw new ValidationException($validation->messages());
            } else {
                return $validation->messages();
            }
        }
        return true;
    }


    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return array(
            'name' => 'required|max:255|min:1|unique:groups,name,' . $id . ',group_id',
            'active' => 'boolean',
            'url' => 'required|url|max:2083|min:1',
            'description' => 'max: 2048'
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