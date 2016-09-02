<?php

namespace App\Http\Controllers\Log;

use App\Filters\QueryFilter;
use App\Contracts\LogManagement\Logger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserActivityLogController extends Controller
{
    protected $logger;
    protected $filter;

    public function __construct(Logger $logger, QueryFilter $filter)
    {
        $this->logger = $logger;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->logger->getDataTablesLogs($this->filter);
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
            $logs = $this->logger->getDataTablesLogsByUser($this->filter, $user);
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
