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

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:read_user', ['only' => ['show']]);
    }

    /**
     * Edit page of my profile
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
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
        return view('user.profile.index')->with(compact(['user']));
    }

    /**
     * Update my profile
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /*TODO validation here*/
        $user = User::findOrFail($id);
        event(new ProfileUpdating($user));
        $user->update($request->all());
        $status = true;
        event(new ProfileUpdated($user));
        return redirect()->route("profile.index");
    }
}
