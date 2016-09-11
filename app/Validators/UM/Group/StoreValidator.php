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
}