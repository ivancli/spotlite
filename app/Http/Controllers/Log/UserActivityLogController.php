<?php

namespace App\Http\Controllers\Log;

use App\Contracts\Repository\Logger\UserActivityLoggerContract;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserActivityLogController extends Controller
{
    protected $userActivityLoggerRepo;
    protected $filter;

    public function __construct(UserActivityLoggerContract $userActivityLoggerContract, QueryFilter $filter)
    {
        $this->middleware('permission:read_user_activity_log', ['only' => ['index', 'show']]);

        $this->userActivityLoggerRepo = $userActivityLoggerContract;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->userActivityLoggerRepo->getDataTablesLogs($this->filter);
            if ($request->wantsJson()) {
                return response()->json($logs);
            } else {
                return $logs;
            }
        } else {
            return view('logs.user_activity.index');
        }
    }

    public function show(Request $request, $user_id)
    {
        $user = User::findOrfail($user_id);
        if ($request->ajax()) {
            $logs = $this->userActivityLoggerRepo->getDataTablesLogsByUser($this->filter, $user);
            if ($request->wantsJson()) {
                return response()->json($logs);
            } else {
                return $logs;
            }
        } else {
            return view('logs.user_activity.index')->with(compact(['user']));
        }
    }
}
