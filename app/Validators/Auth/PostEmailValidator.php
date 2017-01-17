<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/17/2017
 * Time: 4:29 PM
 */

namespace App\Validators\Auth;

use App\Validators\ValidatorAbstract;

class PostEmailValidator extends ValidatorAbstract
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
            'email' => 'required|email|exists:users,email',
        );
    }
}