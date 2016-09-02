<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:read_user', ['only' => ['show']]);
    }

    public function index()
    {
        $user = auth()->user();
        return view('user.account.index')->with(compact(['user']));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.account.show')->with(compact(['user']));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('user.account.edit')->with(compact(['user']));
    }

    public function update(Request $request, $id)
    {
        /*TODO validation here*/
        $user = User::findOrFail($id);
        $user->update($request->all());
        $status = true;
//        event(new ProfileUpdated($user));
        return redirect()->route("profile.index");
    }
}
