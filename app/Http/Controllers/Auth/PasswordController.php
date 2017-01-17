<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Validators\Auth\PostEmailValidator;
use App\Validators\Auth\PostSetPasswordValidator;
use App\Validators\Captcha\ReCaptchaValidator;
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

    public function postEmail(ReCaptchaValidator $captchaValidator, PostEmailValidator $postEmailValidator, Request $request)
    {
        $captchaValidator->validate($request->all());
        $postEmailValidator->validate($request->all());

        $mailingAgentRepo = $this->mailingAgentRepo;

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            function ($user, $token) use ($mailingAgentRepo) {
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
                $password = array(
                    trans($response)
                );
                return response()->json(compact(['status', 'password']), 422);
            } else {
                return compact(['status', 'response']);
            }
        } else {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * For users who register in SpotLite landing page and did not set up password
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSetPasswordPopup()
    {
        if (auth()->user()->set_password != 'n') {
            abort(403);
            return false;
        }
        return view('auth.password_popup');
    }

    public function postSetPassword(PostSetPasswordValidator $postSetPasswordValidator)
    {
        if (auth()->user()->set_password != 'n') {
            abort(403);
            return false;
        }

        $request = request();

        $postSetPasswordValidator->validate($request->all());

        $input = $request->all();
        $user = auth()->user();
        $user->password = bcrypt($input['password']);
        $user->set_password = 'y';
        $user->save();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'user']));
            } else {
                return compact(['status', 'user']);
            }
        } else {
            return redirect()->route('/')->with(compact(['status', 'user']));
        }
    }
}
