<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 2:56 PM
 */

namespace App\Http\Controllers\Log;


use App\Contracts\LogManagement\CrawlerActivityLogger;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrawlerActivityLogController extends Controller
{
    protected $crawlerActivityLogger;
    protected $filter;

    public function __construct(CrawlerActivityLogger $crawlerActivityLogger, QueryFilter $filter)
    {
        $this->crawlerActivityLogger = $crawlerActivityLogger;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->crawlerActivityLogger->getDataTablesLogs($this->filter);
            if ($request->wantsJson()) {
                return response()->json($logs);
            } else {
                return $logs;
            }
        } else {
            return view('logs.crawler_activity.index');
        }
    }

    public function show(Request $request, $user_id)
    {

    }
}