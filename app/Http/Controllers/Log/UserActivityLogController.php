<?php

namespace App\Http\Controllers\Log;

use App\Contracts\LogManagement\UserActivityLogger;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserActivityLogController extends Controller
{
    protected $userActivityLogger;
    protected $filter;

    public function __construct(UserActivityLogger $userActivityLogger, QueryFilter $filter)
    {
        $this->userActivityLogger = $userActivityLogger;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->userActivityLogger->getDataTablesLogs($this->filter);
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
            $logs = $this->userActivityLogger->getDataTablesLogsByUser($this->filter, $user);
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
