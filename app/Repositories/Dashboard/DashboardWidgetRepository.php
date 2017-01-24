<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 4:36 PM
 */

namespace App\Repositories\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardWidgetContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Models\Dashboard\DashboardWidget;

class DashboardWidgetRepository implements DashboardWidgetContract
{
    protected $siteRepo;
    protected $productRepo;

    public function __construct(SiteContract $siteContract, ProductContract $productContract)
    {
        $this->siteRepo = $siteContract;
        $this->productRepo = $productContract;
    }

    public function getWidget($id, $fail = true)
    {
        if ($fail == true) {
            return DashboardWidget::findOrFail($id);
        } else {
            return DashboardWidget::find($id);
        }
    }

    public function getWidgets()
    {
        return DashboardWidget::all();
    }

    public function storeWidget($options)
    {
        return DashboardWidget::create($options);
    }

    public function updateWidget($options, $id)
    {
        $widget = $this->getWidget($id);
        $widget->update($options);
        return $widget;
    }

    public function deleteWidget($id)
    {
        $widget = $this->getWidget($id);
        $widget->delete();
        return true;
    }

    public function getWidgetData($id)
    {
        $widget = $this->getWidget($id);
        $dashboard = $widget->dashboard;

        //this is a chart
        if ($widget->dashboard_widget_type_id == 1) {
            $chartType = $widget->getPreference('chart_type');

            $timespan = $dashboard->getPreference('timespan');
            if (is_null($timespan)) {
                $timespan = $widget->getPreference('timespan');
            }
            $resolution = $dashboard->getPreference('resolution');
            if (is_null($resolution)) {
                $resolution = $widget->getPreference('resolution');
            }

            switch ($timespan) {
                case "this_week":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("monday this week"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
                case "last_week":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("monday last week"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("sunday last week"));
                    break;
                case "last_7_days":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("today - 6 days"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
                case "this_month":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("first day of this month"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
                case "last_month":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("first day of last month"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("last day of last month"));
                    break;
                case "last_30_days":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("today - 29 days"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
                case "this_quarter":
                    $startDateTime = date('Y-m-d 00:00:00', mktime(0, 0, 0, (ceil(date("m") / 4) * 3), 1));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
                case "last_quarter":
                    $startDateTime = date('Y-m-d 00:00:00', mktime(0, 0, 0, ((ceil(date("m") / 4) - 1) * 3), 1));
                    $endDateTime = date("Y-m-d 23:59:59", mktime(0, 0, 0, (ceil(date("m") / 4) * 3), 0));
                    break;
                case "last_90_days":
                    $startDateTime = date("Y-m-d 00:00:00", strtotime("today - 89 days"));
                    $endDateTime = date("Y-m-d 23:59:59", strtotime("today"));
                    break;
            }

            if (isset($startDateTime) && isset($endDateTime)) {

                $user = auth()->user();
                if ($user->needSubscription && $user->subscriptionCriteria()->historic_pricing > 0) {
                    $limitedDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . "-{$user->subscriptionCriteria()->historic_pricing} month"));
                    if (strtotime($startDateTime) < strtotime($limitedDate)) {
                        $startDateTime = $limitedDate;
                    }
                }

                switch ($chartType) {
                    case "site":
                        $site = $widget->site();
                        $site_id = $site->getKey();

                        $sitePrices = array();
                        $historicalPrices = $site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                        foreach ($historicalPrices as $historicalPrice) {
                            switch ($resolution) {
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
                        $data[$site_id]["name"] = parse_url($site->site_url)['host'];
                        foreach ($sitePrices as $dateStamp => $dateLevelPrices) {
                            $data[$site_id]["average"][] = array(
                                strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                            );
                        }
                        usort($data[$site_id]["average"], function ($a, $b) {
                            return $a[0] > $b[0];
                        });
                        return $data;
                        break;
                    case "product":
                        $product = $widget->product();

                        $productPrices = array();
                        $sites = $product->sites;
                        foreach ($sites as $site) {
                            $sitePrices = array();
                            $historicalPrices = $site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                            foreach ($historicalPrices as $historicalPrice) {
                                switch ($resolution) {
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
                            $data[$siteId]["name"] = parse_url($this->siteRepo->getSite($siteId)->site_url)['host'];
                            foreach ($siteLevelPrices as $dateStamp => $dateLevelPrices) {
                                $data[$siteId]["average"][] = array(
                                    strtotime($dateStamp) * 1000, array_sum($dateLevelPrices) / count($dateLevelPrices)
                                );
                            }

                            usort($data[$siteId]["average"], function ($a, $b) {
                                return $a[0] > $b[0];
                            });
                        }
                        return $data;

                        break;
                    case "category":
                        $category = $widget->category();

                        $categoryPrices = array();
                        foreach ($category->products as $product) {
                            $productPrices = array();
                            $sites = $product->sites;
                            foreach ($sites as $site) {
                                $sitePrices = array();
                                $historicalPrices = $site->historicalPrices()->orderBy("created_at", "asc")->whereBetween("created_at", array($startDateTime, $endDateTime))->get();
                                foreach ($historicalPrices as $historicalPrice) {
                                    switch ($resolution) {
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
                        return $data;
                        break;
                }
            }
        }
    }
}