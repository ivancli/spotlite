<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/2/2016
 * Time: 4:43 PM
 */

namespace App\Http\Controllers\User;


use App\Contracts\GroupManagement\GroupManager;
use App\Events\Group\FirstLoginViewed;
use App\Events\Group\GroupAttached;
use App\Events\Group\GroupCreateViewed;
use App\Events\Group\GroupDeleted;
use App\Events\Group\GroupDeleting;
use App\Events\Group\GroupDetached;
use App\Events\Group\GroupEditViewed;
use App\Events\Group\GroupListViewed;
use App\Events\Group\GroupSingleViewed;
use App\Events\Group\GroupStored;
use App\Events\Group\GroupStoring;
use App\Events\Group\GroupUpdated;
use App\Events\Group\GroupUpdating;
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
        $domain = "http://www." . substr(strrchr(auth()->user()->email, "@"), 1);

        $emailClients = config('constants.email_clients');
        if (count(array_intersect(array_map('strtolower', explode(' ', $domain)), $emailClients)) > 0) {
            $domain = "";
        }
        event(new FirstLoginViewed());
        return view('user.group.first_login')->with(compact(['domain']));
    }

    public function index()
    {
        $user = auth()->user();
        $groups = $user->groups;
        event(new GroupListViewed());
        return view('user.group.index')->with(compact(['user', 'groups']));
    }

    public function show($id)
    {
        $group = UMGroup::findOrFail($id);
        event(new GroupSingleViewed($group));
        return view('user.group.show')->with(compact(['group']));
    }

    public function create()
    {
        event(new GroupCreateViewed());
        return view('user.group.create');
    }

    public function store(Request $request)
    {
        /*TODO enhance validator here to user repository pattern*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
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
        $group = UMGroup::where("name", $request->get("name"))->first();
        if (!is_null($group)) {
            auth()->user()->groups()->attach($group->getKey());
            event(new GroupAttached($group));
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
        } else {
            event(new GroupStoring());
            $group = $this->groupManager->createGroup($request->all());
            event(new GroupStored($group));
            auth()->user()->groups()->attach($group->getKey());
            event(new GroupAttached($group));
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
    }

    public function edit($id)
    {
        $group = UMGroup::findOrFail($id);
        if (!in_array($id, auth()->user()->groups->pluck((new UMGroup)->getKeyName())->toArray())) {
            abort(403);
            return false;
        }
        event(new GroupEditViewed($group));
        return view('user.group.edit')->with(compact(['group']));
    }

    public function update(Request $request, $id)
    {
        /*TODO basic validation here*/
        $group = UMGroup::findOrFail($id);
        if (!in_array($id, auth()->user()->groups->pluck((new UMGroup)->getKeyName())->toArray())) {
            abort(403);
            return false;
        }

        if ($group->name != $request->get('name') && !is_null(UMGroup::where('name', $request->get('name'))->first())) {
            $group = UMGroup::where('name', $request->get('name'))->first();
            auth()->user()->groups()->detach($id);
            event(new GroupDetached($group));
            auth()->user()->groups()->attach($group->getKey());
            event(new GroupAttached($group));
        } else {
            if ($group->users->count() > 1) {
                event(new GroupStoring());
                $group = $this->groupManager->createGroup($request->all());
                event(new GroupStored($group));
                auth()->user()->groups()->detach($id);
                event(new GroupDetached($group));
                auth()->user()->groups()->attach($group->getKey());
                event(new GroupAttached($group));
            } else {
                event(new GroupUpdating($group));
                $group = $this->groupManager->updateGroup($id, $request->all());
                event(new GroupUpdated($group));
            }
        }
        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['group', 'status']));
            } else {
                return compact(['group', 'status']);
            }
        } else {
            return redirect()->route('group.edit', $group->getKey())->with(compact(['group']));
        }
    }

    public function destroy(Request $request, $id)
    {
        /*TODO check if there are any users attached to this group*/

        $group = UMGroup::findOrFail($id);
        if (!in_array($id, auth()->user()->groups->pluck((new UMGroup)->getKeyName())->toArray())) {
            abort(403);
            return false;
        }

        $group = UMGroup::findOrFail($id);
        if ($group->users->count() > 1) {
            auth()->user()->groups()->detach($id);
            event(new GroupDetached($group));
            $status = true;
        } else {
            event(new GroupDeleting($group));
            $status = $this->groupManager->destroyGroup($id);
            event(new GroupDeleted($group));
        }
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('group.index');
        }
    }
}