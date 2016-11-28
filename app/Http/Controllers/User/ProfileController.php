<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Events\User\Profile\ProfileEditViewed;
use App\Events\User\Profile\ProfileUpdated;
use App\Events\User\Profile\ProfileUpdating;
use App\Events\User\Profile\ProfileViewed;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Validators\User\Profile\InitUpdateValidator;
use App\Validators\User\Profile\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $mailingAgentRepo;

    public function __construct(MailingAgentContract $mailingAgentContract)
    {
        $this->middleware('permission:read_user', ['only' => ['show']]);
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    /**
     * Edit page of my profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        event(new ProfileViewed($user));
        return view('user.profile.index')->with(compact(['user']));
    }

    public function edit()
    {
        $user = auth()->user();
        event(new ProfileEditViewed($user));
        return view('user.profile.edit')->with(compact(['user']));
    }

    /**
     * Detail page of other users' profile
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        event(new ProfileViewed($user));
        return view('user.profile.show')->with(compact(['user']));
    }

    /**
     * Update my profile
     * @param UpdateValidator $updateValidator
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateValidator $updateValidator, Request $request, $id)
    {
        try {
            $updateValidator->validate($request->all());
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

        $user = User::findOrFail($id);
        event(new ProfileUpdating($user));
        $input = array_except($request->all(), ['email']);
        $user->update($input);
        event(new ProfileUpdated($user));

        $this->mailingAgentRepo->editSubscriber($user->email, array(
            'Name' => $user->first_name . " " . $user->last_name,
        ));

        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'user']));
            } else {
                return compact(['status', 'user']);
            }
        } else {
            return redirect()->route("profile.index");
        }
    }

    public function initialUpdate(InitUpdateValidator $initUpdateValidator, Request $request)
    {
        try {
            $initUpdateValidator->validate($request->all());
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

        $user = auth()->user();
        event(new ProfileUpdating($user));
        $input = array_except($request->all(), ['email']);
        $user->update($input);
        event(new ProfileUpdated($user));

        $this->mailingAgentRepo->editSubscriber($user->email, array(
            'Name' => $user->first_name . " " . $user->last_name,
        ));

        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'user']));
            } else {
                return compact(['status', 'user']);
            }
        } else {
            return redirect()->route("profile.index");
        }
    }
}
