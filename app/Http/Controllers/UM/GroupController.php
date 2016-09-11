<?php
namespace App\Http\Controllers\UM;

use App\Exceptions\ValidationException;
use App\Models\Group;
use App\Models\User;
use App\Validators\UM\Group\StoreValidator;
use App\Validators\UM\Group\UpdateValidator;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMGroupController;
use Illuminate\Http\Request;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class GroupController extends UMGroupController
{
    protected $storeValidator;
    protected $updateValidator;

    public function __construct(StoreValidator $storeValidator, UpdateValidator $updateValidator)
    {
        $this->middleware('permission:create_group', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_group', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_group', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_group', ['only' => ['destroy']]);

        $this->storeValidator = $storeValidator;
        $this->updateValidator = $updateValidator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            $groupCount = Group::count();
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
     * @return bool|\Illuminate\Http\RedirectResponse
     * @internal  param null $route
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
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
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
     * @return \Illuminate\Http\Response|string
     * @internal  param null $route
     */
    public function update(Request $request, $id)
    {
        try {
            $this->updateValidator->validate($request->all());
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
