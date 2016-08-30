<?php
namespace App\Http\Controllers\Chargify;

use App\Http\Controllers\Controller;
use App\Libraries\ChargifyAPI;
use App\Models\Subscription;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 4:21 PM
 */
class VerificationController extends Controller
{
    use ChargifyAPI;

    public function paymentTrigger(Request $request)
    {
        if (!$request->has('ref') || !$request->has('id')) {
            abort(403, "unauthorised access");
        } else {
            $reference = $request->get('ref');
            $reference = json_decode($reference);
            try {
                if (property_exists($reference, 'user_id') && property_exists($reference, 'verification_code')) {
                    $user = User::findOrFail($reference->user_id);
                    if ($user->verification_code == $reference->verification_code) {
                        /* todo enable this once it's live */
                        $user->verification_code = null;
                        $user->save();

                        /*todo UPDATE AND CHECK SUBSCRIPTION STATUS*/
                        $subscription_id = $request->get('id');
                        $subscription = $this->getSubscription($subscription_id);
                        if ($user->subscriptions->count() > 0) {
                            foreach ($user->subscriptions as $userSub) {
                                /*todo find out the not expired one*/


                            }
                        } else {
                            /* create subscription record in DB */
                            $expiry_datetime = $subscription->expires_at;
                            $sub = new Subscription();
                            $sub->user_id = $user->getKey();
                            $sub->api_product_id = $subscription->product->id;
                            $sub->api_customer_id = $subscription->customer->id;
                            $sub->api_subscription_id = $subscription->id;
                            $sub->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_datetime));
                            $sub->save();


                            $title = "Welcome to SpotLite";
                            $bodyTitle = "Welcome to SpotLite";
                            $bodyContent = "[Please put the welcome message here. Chargify indicates that credit card details are correct and payment can be made through.]";
                            $bodyContent .= "<br>The available attributes in this page are subscription, product and user information in Chargify";
                            $bodyContent .= "<br>Please refer to <a href=\"https://docs.chargify.com/api-subscriptions\">https://docs.chargify.com/api-subscriptions</a>
                        for more available attributes.";

                            return view('msg.payment')->with(compact(['title', 'bodyTitle', 'bodyContent']));
//                            return redirect()->route('dashboard.index');
                        }
                    } else {
                        abort(403, "unauthorised access");
                        return false;
                    }
                } else {
                    abort(404, "page not found");
                    return false;
                }

            } catch (ModelNotFoundException $e) {
                abort(404, "page not found");
                return false;
            }

        }
    }
}