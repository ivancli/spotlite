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
use Illuminate\Support\Facades\DB;

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

        if ($request->ajax()) {
            if ($request->wantsJson()) {
                /*TODO validate start date and end date and resolution*/

//                $startDateTime = intval($request->get('start_date'));
//                $endDateTime = intval($request->get('end_date'));
                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));
                $category = $this->categoryManager->getCategory($category_id);
                foreach ($category->products as $product) {
                    $sites = $product->sites;
                    foreach ($sites as $site) {
//                        $historicalPrices = $site->historicalPrices;
                        DB::enableQueryLog();
                        $historicalPrices = $site->historicalPrices()->whereRaw("historical_prices.created_at BETWEEN '?' AND '?'", array($startDateTime, $endDateTime))->get();
                        dump(DB::getQueryLog());
                        dump($historicalPrices->count());
                        foreach ($historicalPrices as $historicalPrice) {
//                            dump($historicalPrice->price);
                        }
                    }
                }


            } else {
                $category = $this->categoryManager->getCategory($category_id);
                return view('charts.category.index')->with(compact(['category']));
            }
        } else {
            $category = $this->categoryManager->getCategory($category_id);
            return view('charts.category.index')->with(compact(['category']));
        }
    }

    public function productIndex(Request $request, $product_id)
    {
        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                $product = $this->productManager->getProduct($product_id);
                return view('charts.product.index')->with(compact(['product']));
            }
        } else {
            $product = $this->productManager->getProduct($product_id);
            return view('charts.product.index')->with(compact(['product']));
        }
    }

    public function productSiteIndex(Request $request, $product_site_id)
    {
        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                $productSite = $this->productSiteManager->getProductSite($product_site_id);
                return view('charts.site.index')->with(compact(['productSite']));
            }
        } else {
            $productSite = $this->productSiteManager->getProductSite($product_site_id);
            return view('charts.site.index')->with(compact(['productSite']));
        }
    }
}