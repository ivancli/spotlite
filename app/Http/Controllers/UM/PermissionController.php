<?php
namespace App\Http\Controllers\UM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMPermissionController;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class PermissionController extends UMPermissionController
{

    public function __construct()
    {
        $this->middleware('permission:create_permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_permission', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_permission', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_permission', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return  \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(Request $request, $format = null)
    {
        if ($request->ajax()) {
            $output = parent::index($request, 'datatable');
            if ($request->wantsJson()) {
                return response()->json($output);
            } else {
                return $output;
            }
        } else {
            return view('um.permission.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = UMRole::pluck('display_name', (new UMRole())->getKeyName());
        $permissions = UMPermission::pluck('display_name', (new UMPermission())->getKeyName());
        return view('um.permission.create')->with(compact(['roles', 'permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  bool|\Illuminate\Http\Response|UMPermission
     */
    public function store(Request $request)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|min:1|unique:permissions,name'
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
            $permission = parent::store($request);
            if ($permission === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['permission', 'status']));
                    } else {
                        return $permission;
                    }
                } else {
                    return redirect()->route('um.permission.index')->with(compact(['permission', 'status']));
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
     */
    public function show(Request $request, $id)
    {
        $permission = parent::show($request, $id);
        if ($permission === false) {
            abort(404);
            return false;
        } else {
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['permission', 'status']));
                } else {
                    return $permission;
                }
            } else {
                return view('um.permission.show')->with(compact(['permission', 'status']));
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param    int $id
     * @return  bool|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = parent::edit($id);
        if ($permission === false) {
            abort(404);
            return false;
        } else {
            $exceptIDs = [$permission->id];
            if (!is_null($permission->childPerms)) {
                $exceptIDs = array_merge($permission->childPerms->pluck((new UMPermission())->getKeyName())->toArray(), $exceptIDs);
            }
            $permissions = UMPermission::all()->except($exceptIDs)->pluck('display_name', (new UMPermission())->getKeyName());
            $roles = UMRole::pluck('display_name', (new UMRole())->getKeyName());
            return view("um.permission.edit")->with(compact(['permission', 'roles', 'permissions']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response|string
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|min:1|unique:permissions,name,' . $id,
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
            $permission = parent::update($request, $id);
            if ($permission === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['permission', 'status']));
                    } else {
                        return $permission;
                    }
                } else {
                    return redirect()->route("um.permission.index")->with(compact(['permission', 'status']));
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
                return redirect()->route('um.permission.index')->with(compact(['status']));
            }
        }
    }
}
