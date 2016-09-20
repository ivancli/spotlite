<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 9:15 AM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\AlertManager;
use App\Jobs\SendMail;
use App\Models\Alert;

class SLAlertManager implements AlertManager
{

    public function getAlerts()
    {
        $alerts = Alert::all();
        return $alerts;
    }

    public function getAlert($alert_id)
    {
        $alert = Alert::findOrFail($alert_id);
        return $alert;
    }

    public function storeAlert($options)
    {
        $alert = Alert::create($options);
        return $alert;
    }

    public function updateAlert($alert_id, $options)
    {
        $alert = $this->getAlert($alert_id);
        $alert->update($options);
        return $alert;
    }

    public function deleteAlert($alert_id)
    {
        $alert = $this->getAlert($alert_id);
        $alert->delete();
    }

    public function triggerProductAlert(Alert $alert)
    {
        $product = $alert->alertable;
        $productSites = $product->productSites;
    }

    public function triggerProductSiteAlert(Alert $alert)
    {
        $productSite = $alert->alertable;
        if (is_null($productSite)) {
            return false;
        }
        $site = $productSite->site;
        $product = $productSite->product;

        $myProductSite = $product->productSites()->where("my_price", "y")->first();


        if ($alert->comparison_price_type == 'my price') {
            if (is_null($myProductSite)) {
                return false;
            }
            /* the alert site is my site, no need to compare or notify */
            if($myProductSite->site->getKey() == $site->getKey()){
                return false;
            }
            $comparisonPrice = $myProductSite->site->recent_price;
        } else {
            $comparisonPrice = $alert->comparison_price;
        }
        if (is_null($comparisonPrice)) {
            return false;
        }

        $alertUser = $this->comparePrices($site->recent_price, $comparisonPrice, $alert->operator);
        if ($alertUser) {
            dispatch((new SendMail('products.alert.email.site', compact(['alert', 'myProductSite']), $product->user, 'SpotLite - Site Price Alert'))->onQueue("mailing"));
        }
    }

    private function comparePrices($priceA, $priceB, $operator)
    {
        switch ($operator) {
            case "=<":
                return $priceA <= $priceB;
                break;
            case "<":
                return $priceA < $priceB;
                break;
            case "=>":
                return $priceA >= $priceB;
                break;
            case ">":
                return $priceA > $priceB;
                break;
            default:
                return false;
        }
    }
}