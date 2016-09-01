<?php

namespace App\Http\Controllers\Log;

use App\Contracts\LogManagement\Logger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserActivityLogController extends Controller
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->logger->getLogs();
            if ($request->wantsJson()) {
                return response()->json($logs);
            } else {
                return $logs;
            }
        } else {
            return view('logs.user_activity.index');
        }
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destory()
    {

    }
}
