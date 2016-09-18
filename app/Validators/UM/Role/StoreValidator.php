<?php
namespace App\Validators\UM\Role;
use App\Exceptions\ValidationException;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 8:57 PM
 */
class StoreValidator extends ValidatorAbstract
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
            'name' => 'required|max:255|min:1|unique:roles,name'
        );
    }

    protected function getMessages()
    {
        return array(
            "name.required" => "Role name is required.",
            "name.max" => "Role name accepts maximum 255 characters",
            "name.min" => "Role name is required.",
            "name.unique" => "Role name already exists",
        );
    }
}