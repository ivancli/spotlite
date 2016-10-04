<?php

namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Report\ReportContract;
use App\Contracts\Repository\Product\Report\ReportTaskContract;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportTaskRepo;
    protected $reportRepo;
    protected $productRepo;
    protected $categoryRepo;

    public function __construct(ReportContract $reportContract,
                                ReportTaskContract $reportTaskContract,
                                CategoryContract $categoryContract,
                                ProductContract $productContract)
    {
        $this->reportRepo = $reportContract;
        $this->reportTaskRepo = $reportTaskContract;
        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;
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
                $category = $this->categoryRepo->getCategory($request->get('category_id'));
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
                $product = $this->productRepo->getProduct($request->get('product_id'));
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

                /*
                 * problematic one
                 * should look for the auth user products and categories
                 * */
                $products = auth()->user()->products()->has("reports")->get();
//                $products = Product::has("reports")->get();
                $categories = auth()->user()->categories()->has("reports")->get();
//                $categories = Category::has("reports")->get();
                $status = true;
                if ($request->wantsJson()) {
                    return response()->json(compact(['products', 'categories', 'status']));
                } else {
                    return compact(['products', 'categories', 'status']);
                }
            }
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
        $filename = $createdAt . $report->file_name . '.' . $report->file_type;
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
