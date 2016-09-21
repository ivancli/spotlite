<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 2:56 PM
 */

namespace App\Http\Controllers\Log;


use App\Contracts\LogManagement\CrawlerLogger;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrawlerLogController extends Controller
{
    protected $crawlerLogger;
    protected $filter;

    public function __construct(CrawlerLogger $crawlerLogger, QueryFilter $filter)
    {
        $this->crawlerLogger = $crawlerLogger;
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