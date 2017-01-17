<?php
namespace App\Validators\Auth;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/9/2016
 * Time: 11:56 AM
 */
class PostSetPasswordValidator extends ValidatorAbstract
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
            'password' => 'required|min:6|confirmed',
        );
    }
}