<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/2/2016
 * Time: 4:43 PM
 */

namespace App\Http\Controllers\User;


use App\Contracts\GroupManagement\GroupManager;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Invigor\UM\UMGroup;
use Validator;

class GroupController extends Controller
{
    protected $groupManager;

    public function __construct(GroupManager $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    public function firstLogin()
    {
        return view('user.group.first_login');
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
        /*TODO enhance validator here to user repository pattern*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:groups,name|max:255',
            'url' => 'required|url|max:2083',
            'description' => 'max:255'
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                $status = false;
                $errors = $validator->errors()->all();
                if ($request->wantsJson()) {
                    return response()->json(compact(['errors', 'status']));
                } else {
                    return compact(['errors', 'status']);
                }
            } else {
                return redirect()->route('group.create')->withErrors($validator)->withInput();
            }
        }
        $group = $this->groupManager->createGroup($request->all());
        auth()->user()->groups()->attach($group->getKey());
        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['group', 'status']));
            } else {
                return compact(['group', 'status']);
            }
        } else {
            return redirect()->route('group.show', $group->getKey());
        }
    }

    public function edit($id)
    {
        $group = UMGroup::findOrFail($id);
        return view('user.group.edit')->with(compact(['group']));
    }

    public function update(Request $request, $id)
    {
        /*TODO basic validation here*/
        $group = UMGroup::findOrFail($id);
        if ($group->users->count() > 1) {
            $group = $this->groupManager->createGroup($request->all());
            auth()->user()->detach($id);
            auth()->user()->attach($group->getKey());
        } else {
            $group = $this->groupManager->updateGroup($id, $request->all());
        }
        /*TODO update this part to user ajax*/
        return redirect()->route('group.edit', $id)->with(compact(['group']));
    }

    public function destroy($id)
    {
        $result = $this->groupManager->destroyGroup($id);
        return redirect()->route('group.index');
    }
}