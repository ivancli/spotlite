<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/19/2016
 * Time: 5:48 PM
 */

namespace App\Http\Controllers\Product;


use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    protected $categoryRepo;
    protected $productRepo;
    protected $siteRepo;

    public function __construct(CategoryContract $categoryContract, ProductContract $productContract, SiteContract $siteContract)
    {
        $this->middleware('permission:read_category_chart', ['only' => ['categoryIndex']]);
        $this->middleware('permission:read_product_chart', ['only' => ['productIndex']]);
        $this->middleware('permission:read_site_chart', ['only' => ['siteIndex']]);


        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;
        $this->siteRepo = $siteContract;
    }

    public function categoryIndex(Request $request, $category_id)
    {

        if ($request->ajax()) {
            if ($request->wantsJson()) {
                /*TODO validate start date and end date and resolution*/

                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));

                $user = auth()->user();
                if ($user->needSubscription && $user->subscriptionCriteria()->historic_pricing > 0) {
                    $limitedDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-{$user->subscriptionCriteria()->historic_pricing} month"));
                    if (strtotime($startDateTime) < strtotime($limitedDate)) {
                        $startDateTime = $limitedDate;
                    }
                }

                $category = $this->categoryRepo->getCategory($category_id);
                $categoryPrices = array();
                foreach ($category->products as $product) {
                    $productPrices = array();
                    $sites = $product->sites;
                    foreach ($sites as $site) {
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
                    $data[$productId]["name"] = $this->productRepo->getProduct($productId)->product_name;
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
                $category = $this->categoryRepo->getCategory($category_id);
                return view('charts.category.index')->with(compact(['category']));
            }
        } else {
            $category = $this->categoryRepo->getCategory($category_id);
            return view('charts.category.index')->with(compact(['category']));
        }
    }

    public function productIndex(Request $request, $product_id)
    {
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));

                $user = auth()->user();
                if ($user->needSubscription && $user->subscriptionCriteria()->historic_pricing > 0) {
                    $limitedDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-{$user->subscriptionCriteria()->historic_pricing} month"));
                    if (strtotime($startDateTime) < strtotime($limitedDate)) {
                        $startDateTime = $limitedDate;
                    }
                }

                $product = $this->productRepo->getProduct($product_id);

                $productPrices = array();
                $sites = $product->sites;
                foreach ($sites as $site) {
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
                    $productPrices[$site->getKey()] = $sitePrices;
                }

                $data = array();
                foreach ($productPrices as $siteId => $siteLevelPrices) {
                    $data[$siteId] = array();
                    $data[$siteId]["average"] = array();
                    $site = $this->siteRepo->getSite($siteId);
                    if (!is_null($site->ebayItem)) {
                        $data[$siteId]["name"] = "eBay: " . $site->ebayItem->seller_username;
                    } else {
                        $data[$siteId]["name"] = is_null($site->userDomainName) ? $site->domain : $site->userDomainName;
                    }
                    foreach ($siteLevelPrices as $dateStamp => $dateLevelPrices) {
                        $data[$siteId]["average"][] = array(
                            strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                        );
                    }

                    usort($data[$siteId]["average"], function ($a, $b) {
                        return $a[0] > $b[0];
                    });
                }
                $status = true;
                return response()->json(compact(['status', 'data']));


            } else {
                $product = $this->productRepo->getProduct($product_id);
                return view('charts.product.index')->with(compact(['product']));
            }
        } else {
            $product = $this->productRepo->getProduct($product_id);
            return view('charts.product.index')->with(compact(['product']));
        }
    }

    public function siteIndex(Request $request, $site_id)
    {
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                $startDateTime = date('Y-m-d H:i:s', intval($request->get('start_date')));
                $endDateTime = date('Y-m-d H:i:s', intval($request->get('end_date')));

                $user = auth()->user();
                if ($user->needSubscription && $user->subscriptionCriteria()->historic_pricing > 0) {
                    $limitedDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-{$user->subscriptionCriteria()->historic_pricing} month"));
                    if (strtotime($startDateTime) < strtotime($limitedDate)) {
                        $startDateTime = $limitedDate;
                    }
                }

                $site = $this->siteRepo->getSite($site_id);

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

                $data[$site_id] = array();
                $data[$site_id]["average"] = array();
                if (!is_null($site->ebayItem)) {
                    $data[$site_id]["name"] = "eBay".$site->ebayItem->seller_username;
                } else {
                    $data[$site_id]["name"] = is_null($site->userDomainName) ? $site->domain : $site->userDomainName;
                }
                foreach ($sitePrices as $dateStamp => $dateLevelPrices) {
                    $data[$site_id]["average"][] = array(
                        strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                    );
                }

                usort($data[$site_id]["average"], function ($a, $b) {
                    return $a[0] > $b[0];
                });
                $status = true;
                return response()->json(compact(['status', 'data']));


            } else {
                $site = $this->siteRepo->getSite($site_id);
                return view('charts.site.index')->with(compact(['site']));
            }
        } else {
            $site = $this->siteRepo->getSite($site_id);
            return view('charts.site.index')->with(compact(['site']));
        }
    }
}