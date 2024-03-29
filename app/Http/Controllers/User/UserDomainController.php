<?php

namespace App\Http\Controllers\User;

use App\Events\User\UserDomain\AfterStore;
use App\Events\User\UserDomain\BeforeStore;
use App\Http\Controllers\Controller;
use App\Models\UserDomain;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserDomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        event(new BeforeStore());

        $domains = $request->get('domains');
        $names = $request->get('names');
        $user = auth()->user();
        $user->domains()->delete();
        foreach ($domains as $key => $domain) {
            if (isset($names[$key]) && !empty($names[$key])) {
                $user->domains()->save(new UserDomain([
                    "domain" => $domain,
                    "name" => isset($names[$key]) && !empty($names[$key]) ? $names[$key] : null,
                ]));
            } else {
                $userDomain = $user->domains()->where('domain', '=', $domain)->first();
                if (!is_null($userDomain)) {
                    $userDomain->delete();
                }
            }
        }

        $status = true;

        event(new AfterStore());

        return compact(['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
