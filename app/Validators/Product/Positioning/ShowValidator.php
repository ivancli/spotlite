<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/28/2017
 * Time: 11:16 AM
 */
namespace App\Validators\Product\Positioning;


use App\Validators\ValidatorAbstract;

class ShowValidator extends ValidatorAbstract
{

    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return [
            'reference' => 'required_with:position'
        ];
    }
}