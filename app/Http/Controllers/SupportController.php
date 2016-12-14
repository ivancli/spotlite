<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/15/2016
 * Time: 9:49 AM
 */

namespace App\Http\Controllers;


use App\Contracts\Repository\Security\TokenContract;
use App\Jobs\SendMail;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected $tokenRepo;

    public function __construct(TokenContract $tokenContract)
    {
        $this->tokenRepo = $tokenContract;
    }

    public function contact_us(Request $request)
    {
//        if (!$request->has('_token') || !$this->tokenRepo->verifyToken($request->get('_token'))) {
//            $status = false;
//            $errors = array(
//                ['Session has expired please refresh and try again.']
//            );
//            if ($request->has('callback')) {
//                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
//            } else if ($request->wantsJson()) {
//                return response()->json(compact(['errors', 'status']));
//            } else {
//                return compact(['errors', 'status']);
//            }
//        }

        $input = $request->all();
        dispatch((new SendMail('support.contact_us',
            $input,
            array(
                "email" => env("SUPPORT_EMAIL_ADDRESS"),
                "subject" => 'SpotLite - Contact Us',
            )
        ))->onQueue("mailing"));

        $status = true;
        if ($request->has('callback')) {
            return response()->json(compact(['status']))->setCallback($request->get('callback'));
        } else if ($request->wantsJson()) {
            return response()->json(compact(['status']));
        } else {
            return compact(['status']);
        }
    }


    public function signUpForBetaTesting(Request $request)
    {
//        if (!$request->has('_token') || !$this->tokenRepo->verifyToken($request->get('_token'))) {
//            $status = false;
//            $errors = array(
//                ['Session has expired please refresh and try again.']
//            );
//            if ($request->has('callback')) {
//                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
//            } else if ($request->wantsJson()) {
//                return response()->json(compact(['errors', 'status']));
//            } else {
//                return compact(['errors', 'status']);
//            }
//        }

        $input = $request->all();
        dispatch((new SendMail('support.sign_up_for_beta',
            $input,
            array(
                "email" => env("SUPPORT_EMAIL_ADDRESS"),
                "subject" => 'SpotLite - Sign Up For Beta Notification',
            )
        ))->onQueue("mailing"));

        $status = true;
        if ($request->has('callback')) {
            return response()->json(compact(['status']))->setCallback($request->get('callback'));
        } else if ($request->wantsJson()) {
            return response()->json(compact(['status']));
        } else {
            return compact(['status']);
        }
    }
}