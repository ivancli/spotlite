<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/16/2017
 * Time: 11:22 AM
 */

namespace App\Http\Controllers\API;


use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Http\Controllers\Controller;
use App\Validators\API\Subscription\VerifyCouponValidator;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    var $request;
    var $subscriptionRepo;

    public function __construct(Request $request, SubscriptionContract $subscriptionContract)
    {
        $this->request = $request;
        $this->subscriptionRepo = $subscriptionContract;
    }

    public function verifyCoupon(VerifyCouponValidator $verifyCouponValidator)
    {
        $verifyCouponValidator->validate($this->request->all());
        $data = $this->request->all();
        $couponCode = $data['coupon_code'];
        $productFamilyId = $data['product_family_id'];

        $isValid = $this->subscriptionRepo->validateCoupon($couponCode, $productFamilyId);

        if ($this->request->has('callback')) {
            return response()->json(compact(['isValid']))->setCallback($this->request->get('callback'));
        } else {
            return compact(['isValid']);
        }
    }
}