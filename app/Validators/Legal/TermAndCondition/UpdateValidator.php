<?php
namespace App\Validators\Legal\TermAndCondition;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/17/2017
 * Time: 3:48 PM
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
            'content' => 'required'
        );
    }
}