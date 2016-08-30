<?php
namespace App\Http\Controllers\UM;

use App\User;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMGroupController;
use Illuminate\Http\Request;
use Invigor\UM\UMGroup;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class GroupController extends UMGroupController
{
    public function __construct()
    {
        $this->middleware('permission:create_group', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_group', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_group', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_group', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return  \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|static[]
     * @internal  param null $view
     */
    public function index(Request $request, $format = null)
    {
        if ($request->ajax()) {
            $output = parent::index($request, "datatable");
            if ($request->wantsJson()) {
                return response()->json($output);
            } else {
                return $output;
            }
        } else {
            $userCount = User::count();
            $groupCount = UMGroup::count();
            $roleCount = UMRole::count();
            $permissionCount = UMPermission::count();
            return view('um.group.index')->with(compact('userCount', 'groupCount', 'roleCount', 'permissionCount'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return  \Illuminate\Http\Response
     * @internal  param null $view
     */
    public function create()
    {
        return view('um.group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  bool
     * @internal  param null $route
     */
    public function store(Request $request)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:groups|max:255|min:1',
            'active' => 'boolean',
            'url' => 'required|url|max:2083|min:1',
            'description' => 'max:255'
        ]);
        if ($validator->fails()) {
            $status = false;
            if ($request->ajax()) {
                $errors = $validator->errors()->all();
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return $errors;
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            $group = parent::store($request);
            if ($group === false) {
                abort(404);
                return false;
            } else {
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['group', 'status']));
                    } else {
                        return $group;
                    }
                } else {
                    return redirect()->route('um.group.index')->with(compact(['group', 'status']));
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @param    int $id
     * @return  string
     * @internal  param null $view
     */
    public function show(Request $request, $id)
    {
        $group = parent::show($request, $id);
        if ($group === false) {
            abort(404);
            return false;
        } else {
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['group', 'status']));
                } else {
                    return $group;
                }
            } else {
                return view('um.group.show')->with(compact(['group', 'status']));
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param    int $id
     * @return  bool|\Illuminate\Http\Response
     * @internal  param null $view
     */
    public function edit($id)
    {
        $group = parent::edit($id);
        if ($group === false) {
            abort(404);
            return false;
        } else {
            return view('um.group.edit')->with(compact(['group']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response|string
     * @internal  param null $route
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:1|unique:groups,name,' . $id . ',group_id',
            'active' => 'boolean',
            'url' => 'required|url|max:2083|min:1',
            'description' => 'max: 2048'
        ]);
        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors()->all();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['errors', 'status']));
                } else {
                    return $errors;
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            $group = parent::update($request, $id);
            if ($group === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['group', 'status']));
                    } else {
                        return $group;
                    }
                } else {
                    return redirect()->route("um.group.index")->with(compact(['group', 'status']));
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param    int $id
     * @return  bool|\Illuminate\Http\Response
     * @internal  param null $route
     */
    public function destroy(Request $request, $id)
    {
        $status = parent::destroy($request, $id);
        if ($status === false) {
            abort(404);
            return false;
        } else {
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return $status;
                }
            } else {
                return redirect()->route('um.group.index')->with(compact(['status']));
            }
        }
    }
}
