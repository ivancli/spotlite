<?php
namespace App\Http\Controllers\UM;

use App\Exceptions\ValidationException;
use App\Models\Group;
use App\Models\User;
use App\Validators\UM\Role\UpdateValidator;
use App\Validators\UM\Role\StoreValidator;
use Illuminate\Http\Request;
use Invigor\UM\Controllers\UMRoleController;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class RoleController extends UMRoleController
{
    protected $storeValidator;
    protected $updateValidator;

    public function __construct(StoreValidator $storeValidator, UpdateValidator $updateValidator)
    {
        $this->middleware('permission:create_role', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_role', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_role', ['only' => ['destroy']]);

        $this->storeValidator = $storeValidator;
        $this->updateValidator = $updateValidator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|\Illuminate\View\View|static[]
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
            return view('um.role.index')->with(compact('userCount', 'groupCount', 'roleCount', 'permissionCount'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
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
     * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
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
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
        try {
            $input = $request->all();
            $input['role_id'] = $id;
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
