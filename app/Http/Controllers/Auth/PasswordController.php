<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Validators\Auth\PostSetPasswordValidator;
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

        $this->validate($request, ['email' => 'required|email|exists:users,email'], array(
            "email.exists" => "This email address is not registered on SpotLite."
        ));

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

        try {
            $postSetPasswordValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

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
