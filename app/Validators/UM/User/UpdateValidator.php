<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 9:06 PM
 */

namespace App\Validators\UM\User;


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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',' . 'user_id',
            'password' => 'min:6|confirmed',
        );
    }

    protected function getMessages()
    {
        return array(
            "first_name.required" => "First name is required.",
            "first_name.max" => "First name accepts maximum 255 characters.",
            "last_name.required" => "Last name is required.",
            "last_name.max" => "Last name accepts maximum 255 characters.",
            "email.required" => "Email is required.",
            "email.email" => "Please enter a valid email address.",
            "email.max" => "Email accepts maximum 255 characters.",
            "email.unique" => "Email already exists.",
            "password.required" => "Password is required.",
            "password.min" => "Password is required.",
        );
    }
}