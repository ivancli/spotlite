<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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

    use ResetsPasswords, CommonFunctions;

    protected $redirectTo = '/';
    protected $mailingAgentRepo;

    /**
     * Create a new password controller instance.
     * @param MailingAgentContract $mailingAgentContract
     */
    public function __construct(MailingAgentContract $mailingAgentContract)
    {
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    public function postEmail(Request $request)
    {
        $response = $this->sendCurl(config('google_captcha.verification_url'), array(
            'method' => 'post',
            'fields' => array(
                'secret' => config('google_captcha.secret_key'),
                'response' => $request->get('g-recaptcha-response')
            ),
        ));
        $response = json_decode($response);
        if ($response == false || !isset($response->success) || $response->success != true) {
            $status = false;
            $errors = array("Please verify that you are not a robot.");
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return false;
            }
        }

        $this->validateSendResetLinkEmail($request);
        $broker = $this->getBroker();

        $mailingAgentRepo = $this->mailingAgentRepo;

        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            function ($m, $user, $token) use ($mailingAgentRepo) {
                $mailingAgentRepo->sendResetPasswordEmail($user, $token);
            }
        );
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
            return false;
        }

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
