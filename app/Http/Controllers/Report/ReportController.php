<?php

namespace App\Http\Controllers\Report;

use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\ReportManager;
use App\Contracts\ProductManagement\ReportTaskManager;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportTaskManager;
    protected $categoryManager;
    protected $productManager;
    protected $reportManager;

    public function __construct(ReportManager $reportManager, ReportTaskManager $reportTaskManager, CategoryManager $categoryManager, ProductManager $productManager)
    {
        $this->reportManager = $reportManager;
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->reportTaskManager = $reportTaskManager;
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

            if ($request->has('category_id')) {
                $category = $this->categoryManager->getCategory($request->get('category_id'));
                $reports = $category->reports;
                $reports->each(function ($item, $key) {
                    unset($item->content);
                });
                $status = true;
                if ($request->wantsJson()) {
                    return response()->json(compact(['reports', 'status']));
                } else {
                    return compact(['reports', 'status']);
                }
            } elseif ($request->has('product_id')) {
                $product = $this->productManager->getProduct($request->get('product_id'));
                $reports = $product->reports;
                $reports->each(function ($item, $key) {
                    unset($item->content);
                });
                $status = true;
                if ($request->wantsJson()) {
                    return response()->json(compact(['reports', 'status']));
                } else {
                    return compact(['reports', 'status']);
                }
            } else {
                $products = Product::has("reports")->get();
                $categories = Category::has("reports")->get();
                $status = true;
                if ($request->wantsJson()) {
                    return response()->json(compact(['products', 'categories', 'status']));
                } else {
                    return compact(['products', 'categories', 'status']);
                }
            }
        } else {

            return view('report.index');
        }
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $report = $this->reportManager->getReport($id);
        if ($report->reportable->user_id != auth()->user()->getKey()) {
            abort(403);
        }
        return response(base64_decode($report->content))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename={$report->file_name}.{$report->file_type}")
            ->header('Expires', 0)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Cache-Control', 'private', false);
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
