<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/22/2016
 * Time: 5:23 PM
 */

namespace App\Http\Controllers\Product;


use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportTaskController extends Controller
{
    protected $categoryManager;
    protected $productManager;

    public function __construct(CategoryManager $categoryManager, ProductManager $productManager)
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
    }

    /**
     * Show Edit Category Report Popup
     *
     * @param Request $request
     * @param $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCategoryReport(Request $request, $category_id)
    {
        $category = $this->categoryManager->getCategory($category_id);
        return view('products.report.category')->with(compact(['category']));
    }

    /**
     * Update Category Report Settings
     *
     * @param Request $request
     * @param $category_id
     */
    public function updateCategoryReport(Request $request, $category_id)
    {

    }

    public function deleteCategoryReport(Request $request, $category_id)
    {
        $category = $this->categoryManager->getCategory($category_id);

    }

    /**
     * Show Edit Product Report Popup
     *
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProductReport(Request $request, $product_id)
    {
        $product = $this->productManager->getProduct($product_id);
        return view('products.report.product')->with(compact(['product']));
    }

    public function updateProductReport()
    {

    }

    public function deleteProductReport(Request $request, $product_id)
    {
        $product = $this->productManager->getProduct($product_id);
    }
}