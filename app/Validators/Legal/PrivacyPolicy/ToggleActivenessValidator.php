<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/18/2017
 * Time: 3:52 PM
 */

namespace App\Validators\Legal\PrivacyPolicy;


use App\Validators\ValidatorAbstract;

class ToggleActivenessValidator extends ValidatorAbstract
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
            'active' => 'required'
        );
    }
}