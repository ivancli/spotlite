<?php
namespace App\Http\Controllers\UM;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMRoleController;
use Invigor\UM\UMGroup;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class RoleController extends UMRoleController
{

    public function __construct()
    {
        $this->middleware('permission:create_role', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_role', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_role', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return  \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|static[]
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
            $userCount = User::count();
            $groupCount = UMGroup::count();
            $roleCount = UMRole::count();
            $permissionCount = UMPermission::count();
            return view('um.role.index')->with(compact('userCount', 'groupCount', 'roleCount', 'permissionCount'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = UMPermission::pluck('display_name', (new UMPermission())->getKeyName());
        return view('um.role.create')->with(compact(['permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  bool|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|min:1|unique:roles,name'
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
            $role = parent::store($request);
            if ($role === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['role', 'status']));
                    } else {
                        return $role;
                    }
                } else {
                    return redirect()->route('um.role.index')->with(compact(['role', 'status']));
                }
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response|string
     */
    public function show(Request $request, $id)
    {
        $role = parent::show($request, $id);
        if ($role === false) {
            abort(404);
            return false;
        } else {
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['role', 'status']));
                } else {
                    return $role;
                }
            } else {
                return view('um.role.show')->with(compact(['role', 'status']));
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
        $role = parent::edit($id);
        if ($role === false) {
            abort(404);
            return false;
        } else {
            $permissions = UMPermission::pluck('display_name', (new UMPermission())->getKeyName());
            return view("um.role.edit")->with(compact(['role', 'permissions']));
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
                'name' => 'required|max:255|min:1|unique:roles,name,' . $id . ',role_id',
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
            $role = parent::update($request, $id);
            if ($role === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['role', 'status']));
                    } else {
                        return $role;
                    }
                } else {
                    return redirect()->route("um.role.index")->with(compact(['role', 'status']));
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
                return redirect()->route('um.role.index')->with(compact(['status']));
            }
        }
    }
}
