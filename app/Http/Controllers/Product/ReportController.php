<?php

namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Report\ReportContract;
use App\Contracts\Repository\Product\Report\ReportTaskContract;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportTaskRepo;
    protected $reportRepo;
    protected $productRepo;
    protected $categoryRepo;
    protected $queryFilter;

    public function __construct(ReportContract $reportContract, ReportTaskContract $reportTaskContract, CategoryContract $categoryContract, ProductContract $productContract, QueryFilter $queryFilter)
    {
        $this->middleware('permission:read_report', ['only' => ['index', 'show']]);
        $this->middleware('permission:delete_report', ['only' => ['destroy']]);

        $this->reportRepo = $reportContract;
        $this->reportTaskRepo = $reportTaskContract;
        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;

        $this->queryFilter = $queryFilter;
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
            $reports = $this->reportRepo->getReports($this->queryFilter);
            return response()->json($reports);
        } else {
            return view('products.report.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $report = $this->reportRepo->getReport($id);
        if ($report->reportable->user_id != auth()->user()->getKey()) {
            abort(403);
        }
        $createdAt = date('Ymd', strtotime($report->created_at));
        $filename = $createdAt . "_" . $report->file_name . '.' . $report->file_type;
        return response(base64_decode($report->content))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=$filename")
            ->header('Expires', 0)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Cache-Control', 'private', false);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $report = $this->reportRepo->getReport($id);
        if ($report->reportable->user->getKey() != auth()->user()->getKey()) {
            abort(403);
        }

        $this->reportRepo->deleteReport($id);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }
}
