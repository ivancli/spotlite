<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/19/2016
 * Time: 5:48 PM
 */

namespace App\Http\Controllers\Product;


use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\ProductSiteManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    protected $categoryManager;
    protected $productManager;
    protected $productSiteManager;

    public function __construct(CategoryManager $categoryManager, ProductManager $productManager, ProductSiteManager $productSiteManager)
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->productSiteManager = $productSiteManager;
    }

    public function categoryIndex(Request $request, $category_id)
    {
        $category = $this->categoryManager->getCategory($category_id);

        return view('charts.category.index')->with(compact(['category']));
    }

    public function productIndex(Request $request, $product_id)
    {
        $product = $this->productManager->getProduct($product_id);

        return view('charts.product.index')->with(compact(['product']));
    }

    public function productSiteIndex(Request $request, $product_site_id)
    {
        $productSite = $this->productSiteManager->getProductSite($product_site_id);

        return view('charts.site.index')->with(compact(['productSite']));
    }
}