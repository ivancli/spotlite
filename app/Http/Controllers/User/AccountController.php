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
        $computedDomains = $user->sites->pluck('domain');
        $userDomains = $user->domains;
        $domains = array();
        foreach ($computedDomains as $computedDomain) {
            $userDomain = $userDomains->filter(function ($userDomain, $key) use ($computedDomain) {
                return $userDomain->domain == $computedDomain;
            })->first();

            if (!is_null($userDomain)) {
                $domains[$computedDomain] = $userDomain->name;
            } else {
                $domains[$computedDomain] = null;
            }
        }
        return view('user.account.index')->with(compact(['user', 'domains', 'userDomains']));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.account.show')->with(compact(['user']));
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
