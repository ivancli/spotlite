<?php
namespace App\Http\Controllers\UM;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\Controllers\UMUserController;
use Invigor\UM\UMGroup;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class UserController extends UMUserController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create_user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @param  null $format
     * @return \Illuminate\Http\Response|\stdClass
     * @internal  param null $view
     */
    public function index(Request $request, $format = null)
    {
        if ($request->ajax()) {

            $users = User::when($request->has('start'), function ($query) use ($request) {
                return $query->skip($request->get('start'));
            })
                ->when($request->has('length'), function ($query) use ($request) {
                    return $query->take($request->get('length'));
                })
                ->when($request->has('search'), function ($query) use ($request) {
                    return $query->where('first_name', 'LIKE', "%{$request->get('search')['value']}%")
                        ->orwhere('last_name', 'LIKE', "%{$request->get('search')['value']}%")
                        ->orwhere('email', 'LIKE', "%{$request->get('search')['value']}%");
                })
                ->when($request->has('order') && is_array($request->get('order')), function ($query) use ($request) {
                    $order = $request->get('order');
                    $columns = $request->get('columns');
                    foreach ($order as $index => $ord) {
                        if (isset($ord['column']) && isset($columns[$ord['column']])) {
                            $name = $columns[$ord['column']]['name'];
                            $direction = $ord['dir'];
                            $query->orderBy($name, $direction);
                        }
                    }
                    return $query;
                })->get();
            $users->each(function ($user, $key) {
                $user->urls = array(
                    "show" => route('um.user.show', $user->getKey()),
                    "edit" => route('um.user.edit', $user->getKey()),
                    "delete" => route('um.user.destroy', $user->getKey())
                );
            });
            $output = new \stdClass();
            $output->draw = (int)($request->has('draw') ? $request->get('draw') : 0);
            $output->recordsTotal = User::count();
            if ($request->has('search') && $request->get('search')['value'] != '') {
                $output->recordsFiltered = $users->count();
            } else {
                $output->recordsFiltered = User::count();
            }
            $output->data = $users->toArray();

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
            return view('um.user.index')->with(compact('userCount', 'groupCount', 'roleCount', 'permissionCount'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return  \Illuminate\Http\Response
     * @internal  param null $view
     */
    public function create()
    {
        $groups = UMGroup::pluck('name', (new UMGroup)->getKeyName());
        $roles = UMRole::pluck('display_name', (new UMRole())->getKeyName());
        return view('um.user.create')->with(compact(['groups', 'roles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param  null $route
     * @return  bool|\Illuminate\Http\Response
     * @internal  param null $view
     */
    public function store(Request $request, $route = null)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
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
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } else {
            /* insert */
            $user = parent::store($request);
            if ($user === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['user', 'status']));
                    } else {
                        return $user;
                    }
                } else {
                    return redirect()->route('um.user.index')->with(compact(['user', 'status']));
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
     * @internal  param null $view
     */
    public function show(Request $request, $id)
    {
        $user = parent::show($request, $id);
        if ($user === false) {
            abort(404, "Page not found");
            return false;
        } else {
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['user', 'status']));
                } else {
                    return $user;
                }
            } else {
                return view('um.user.show')->with(compact(['user', 'status']));
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
        $user = parent::edit($id);
        if ($user === false) {
            abort(404);
            return false;
        } else {
            $groups = UMGroup::pluck('name', (new UMGroup)->getKeyName());
            $roles = UMRole::pluck('display_name', (new UMRole)->getKeyName());
            return view("um.user.edit")->with(compact(['user', 'groups', 'roles']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  string
     * @internal  param null $route
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',' . (new User)->getKeyName(),
            'password' => 'min:6|confirmed',
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
            $user = parent::update($request, $id);
            if ($user === false) {
                abort(404);
                return false;
            } else {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['user', 'status']));
                    } else {
                        return $user;
                    }
                } else {
                    return redirect()->route("um.user.index")->with(compact(['user', 'status']));
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param    int $id
     * @return  bool
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
                return redirect()->route('um.user.index')->with(compact(['status']));
            }
        }
    }
}
