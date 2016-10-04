<?php

namespace App\Http\Controllers\Log;

use App\Contracts\Repository\Logger\AlertActivityLoggerContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

class AlertActivityLogController extends Controller
{
    protected $alertActivityLoggerRepo;

    public function __construct(AlertActivityLoggerContract $alertActivityLoggerContract)
    {
        $this->alertActivityLoggerRepo = $alertActivityLoggerContract;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $alertLogs = $this->alertActivityLoggerRepo->getDataTableAlertActivityLogs();
            if ($request->wantsJson()) {
                return response()->json($alertLogs);
            } else {
                return compact($alertLogs);
            }
        } else {
            /*todo implement this if necessary*/
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
