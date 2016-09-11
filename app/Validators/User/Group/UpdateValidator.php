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
     * @return array
     */
    protected function getRules()
    {
        return array(
            'name' => 'required|max:255',
            'url' => 'required|url|max:2083',
            'description' => 'max:255'
        );
    }
}