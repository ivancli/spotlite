<?php

namespace App\Http\Controllers\User;

use App\Events\User\Profile\ProfileEditViewed;
use App\Events\User\Profile\ProfileUpdated;
use App\Events\User\Profile\ProfileUpdating;
use App\Events\User\Profile\ProfileViewed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:read_user', ['only' => ['show']]);
    }

    /**
     * Edit page of my profile
     * @return $this
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
     * @return $this
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        event(new ProfileViewed($user));
        return view('user.profile.show')->with(compact(['user']));
    }

    /**
     * Update my profile
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                $status = false;
                $errors = $validator->errors()->all();
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        }


        $user = User::findOrFail($id);
        event(new ProfileUpdating($user));
        $user->update($request->all());
        event(new ProfileUpdated($user));
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
