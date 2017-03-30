<?php
namespace App\Validators\API\Subscription;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/16/2017
 * Time: 11:23 AM
 */
class VerifyCouponValidator extends ValidatorAbstract
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
            'coupon_code' => 'required',
            'product_family_id' => 'required'
        ];
    }
}