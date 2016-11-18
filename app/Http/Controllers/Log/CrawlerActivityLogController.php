<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 2:56 PM
 */

namespace App\Http\Controllers\Log;


use App\Contracts\Repository\Logger\CrawlerActivityLoggerContract;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrawlerActivityLogController extends Controller
{
    protected $crawlerActivityLoggerRepo;
    protected $filter;

    public function __construct(CrawlerActivityLoggerContract $crawlerActivityLoggerContract, QueryFilter $filter)
    {
        $this->middleware('permission:read_crawler_log', ['only' => ['index']]);

        $this->crawlerActivityLoggerRepo = $crawlerActivityLoggerContract;
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->crawlerActivityLoggerRepo->getDataTablesLogs($this->filter);
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