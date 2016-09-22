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
                $categoryPrices = array();
                foreach ($category->products as $product) {
                    $productPrices = array();
                    $productSites = $product->productSites;
                    foreach ($productSites as $productSite) {
                        $sitePrices = array();
                        $historicalPrices = $productSite->site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                        foreach ($historicalPrices as $historicalPrice) {
                            switch ($request->get('resolution')) {
                                case "weekly":
                                    $date = date('Y-\WW', strtotime($historicalPrice->created_at));
                                    break;
                                case "monthly":
                                    $date = date('Y-m', strtotime($historicalPrice->created_at));
                                    break;
                                case "daily":
                                default:
                                    $date = date('Y-m-d', strtotime($historicalPrice->created_at));
                            }
                            $sitePrices[$date] [] = $historicalPrice->price;
                            unset($date);
                        }

                        foreach ($sitePrices as $date => $sitePrice) {
                            $sum = array_sum($sitePrice);
                            $count = count($sitePrice);
                            $productPrices[$date][] = $sum / $count;
                        }
                    }
                    $categoryPrices[$product->getKey()] = $productPrices;
                }

                $data = array();
                foreach ($categoryPrices as $productId => $productLevelPrices) {
                    $data[$productId] = array();
                    $data[$productId]["range"] = array();
                    $data[$productId]["average"] = array();
                    $data[$productId]["name"] = $this->productManager->getProduct($productId)->product_name;
                    foreach ($productLevelPrices as $dateStamp => $dateLevelPrices) {
                        $data[$productId]["range"][] = array(
                            strtotime($dateStamp) * 1000, min($dateLevelPrices), max($dateLevelPrices)
                        );
                        $data[$productId]["average"][] = array(
                            strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                        );
                    }

                    usort($data[$productId]["range"], function ($a, $b) {
                        return $a[0] > $b[0];
                    });
                    usort($data[$productId]["average"], function ($a, $b) {
                        return $a[0] > $b[0];
                    });
                }
                $status = true;
                return response()->json(compact(['status', 'data']));
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
                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));

                $product = $this->productManager->getProduct($product_id);

                $productPrices = array();
                $productSites = $product->productSites;
                foreach ($productSites as $productSite) {
                    $sitePrices = array();
                    $historicalPrices = $productSite->site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                    foreach ($historicalPrices as $historicalPrice) {
                        switch ($request->get('resolution')) {
                            case "weekly":
                                $date = date('Y-\WW', strtotime($historicalPrice->created_at));
                                break;
                            case "monthly":
                                $date = date('Y-m', strtotime($historicalPrice->created_at));
                                break;
                            case "daily":
                            default:
                                $date = date('Y-m-d', strtotime($historicalPrice->created_at));
                        }
                        $sitePrices[$date] [] = $historicalPrice->price;
                        unset($date);
                    }

                    foreach ($sitePrices as $date => $sitePrice) {
                        $sum = array_sum($sitePrice);
                        $count = count($sitePrice);
                        $sitePrices[$date][] = $sum / $count;
                    }
                    $productPrices[$productSite->getKey()] = $sitePrices;
                }

                $data = array();
                foreach ($productPrices as $productSiteId => $siteLevelPrices) {
                    $data[$productSiteId] = array();
                    $data[$productSiteId]["average"] = array();
                    $data[$productSiteId]["name"] = parse_url($this->productSiteManager->getProductSite($productSiteId)->site->site_url)['host'];
                    foreach ($siteLevelPrices as $dateStamp => $dateLevelPrices) {
                        $data[$productSiteId]["average"][] = array(
                            strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                        );
                    }

                    usort($data[$productSiteId]["average"], function ($a, $b) {
                        return $a[0] > $b[0];
                    });
                }
                $status = true;
                return response()->json(compact(['status', 'data']));


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
                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));

                $productSite = $this->productSiteManager->getProductSite($product_site_id);
                $site = $productSite->site;

                $sitePrices = array();
                $historicalPrices = $site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                foreach ($historicalPrices as $historicalPrice) {
                    switch ($request->get('resolution')) {
                        case "weekly":
                            $date = date('Y-\WW', strtotime($historicalPrice->created_at));
                            break;
                        case "monthly":
                            $date = date('Y-m', strtotime($historicalPrice->created_at));
                            break;
                        case "daily":
                        default:
                            $date = date('Y-m-d', strtotime($historicalPrice->created_at));
                    }
                    $sitePrices[$date] [] = $historicalPrice->price;
                    unset($date);
                }

                foreach ($sitePrices as $date => $sitePrice) {
                    $sum = array_sum($sitePrice);
                    $count = count($sitePrice);
                    $sitePrices[$date][] = $sum / $count;
                }

                $data[$product_site_id] = array();
                $data[$product_site_id]["average"] = array();
                $data[$product_site_id]["name"] = parse_url($this->productSiteManager->getProductSite($product_site_id)->site->site_url)['host'];
                foreach ($sitePrices as $dateStamp => $dateLevelPrices) {
                    $data[$product_site_id]["average"][] = array(
                        strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                    );
                }

                usort($data[$product_site_id]["average"], function ($a, $b) {
                    return $a[0] > $b[0];
                });
                $status = true;
                return response()->json(compact(['status', 'data']));


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