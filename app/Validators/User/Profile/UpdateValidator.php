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
     * @return array
     */
    protected function getRules()
    {
        return array(
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
        );
    }
}