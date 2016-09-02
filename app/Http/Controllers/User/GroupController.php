<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/2/2016
 * Time: 4:43 PM
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Invigor\UM\UMGroup;

class GroupController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $user = auth()->user();
        $groups = $user->groups;
        return view('user.group.index')->with(compact(['user', 'groups']));
    }

    public function show($id)
    {
        $group = UMGroup::findOrFail($id);
        return view('user.group.show')->with(compact(['group']));
    }

    public function create()
    {
        return view('user.group.create');
    }

    public function store(Request $request)
    {
        /*TODO create validation here*/

        $group = UMGroup::create($request->all());

        auth()->user()->groups()->attach($group);

        return redirect()->route('group.show', $group->getKey());
    }

    public function edit($id)
    {
        $group = UMGroup::findOrFail($id);
        return view('user.group.edit')->with(compact(['group']));
    }

    public function update()
    {

    }

    public function destroy($id)
    {

    }
}