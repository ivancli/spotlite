<?php

namespace App\Http\Controllers\User;

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
     * @return $this
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        return view('user.profile.edit')->with(compact(['user']));
    }

    /**
     * Detail page of other users' profile
     * @param $id
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
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
        $user->update($request->all());
        $status = true;
        return redirect()->route("profile.index");
    }
}
