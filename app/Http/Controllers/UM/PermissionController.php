<?php
namespace App\Http\Controllers\UM;

use App\Exceptions\ValidationException;
use App\Models\Group;
use App\Models\User;
use App\Validators\UM\Permission\StoreValidator;
use App\Validators\UM\Permission\UpdateValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMPermissionController;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class PermissionController extends UMPermissionController
{
    protected $storeValidator;
    protected $updateValidator;

    public function __construct(StoreValidator $storeValidator, UpdateValidator $updateValidator)
    {
        $this->middleware('permission:create_permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_permission', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_permission', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_permission', ['only' => ['destroy']]);

        $this->storeValidator = $storeValidator;
        $this->updateValidator = $updateValidator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Database\Eloquent\Collection|\Illuminate\View\View|static[]
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
            $groupCount = Group::count();
            $roleCount = UMRole::count();
            $permissionCount = UMPermission::count();
            return view('um.permission.index')->with(compact('userCount', 'groupCount', 'roleCount', 'permissionCount'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $roles = UMRole::pluck('display_name', (new UMRole())->getKeyName());
        $permissions = UMPermission::pluck('display_name', (new UMPermission())->getKeyName());
//        $roles = array_prepend($roles, array('', ''));
//        $permissions = array_prepend($permissions, array('', ''));
        return view('um.permission.create')->with(compact(['roles', 'permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @param    \Illuminate\Http\Request $request
     * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|UMPermission
     */
    public function store(Request $request)
    {
        try {
            $this->storeValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }
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
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
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
     * @param UpdateValidator $updateValidator
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return \Illuminate\Http\Response|string
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            $input['id'] = $id;
            $this->updateValidator->validate($input);
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }
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
