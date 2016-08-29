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
class VerifyController extends Controller
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
                        $user->verification_code = null;
                        $user->save();

                        /*todo UPDATE AND CHECK SUBSCRIPTION STATUS*/
                        $subscription_id = $request->get('id');
                        $subscription = $this->getSubscription($subscription_id);
                        dump($subscription);
                        dump($user->subscriptions);
                        if ($user->subscriptions->count() > 0) {
                            foreach ($user->subscriptions as $userSub) {
                                /*todo find out the not expired one*/
                            }
                        }else{
                            $sub = new Subscription();
                            /*todo save a new subscription record*/
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