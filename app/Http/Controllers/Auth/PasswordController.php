<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    protected function getSendResetLinkEmailSuccessResponse($response)
    {
        $status = true;
        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json(compact(['status', 'response']));
            } else {
                return compact(['status', 'response']);
            }
        } else {
            return redirect()->back()->with('status', trans($response));
        }
    }

    protected function getSendResetLinkEmailFailureResponse($response)
    {
        $status = false;
        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json(compact(['status', 'response']));
            } else {
                return compact(['status', 'response']);
            }
        } else {
            return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    protected function getResetSuccessResponse($response)
    {
        $status = true;
        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json(compact(['status', 'response']));
            } else {
                return compact(['status', 'response']);
            }
        } else {
            return redirect($this->redirectPath())->with('status', trans($response));
        }
    }


    protected function getResetFailureResponse(Request $request, $response)
    {
        $status = false;
        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json(compact(['status', 'response']));
            } else {
                return compact(['status', 'response']);
            }
        } else {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
        }
    }

}
