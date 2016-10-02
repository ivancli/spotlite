<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 9:15 AM
 */

namespace App\Repositories\Product\Alert;


use App\Contracts\Repository\Product\Alert\AlertContract;
use App\Jobs\LogUserActivity;
use App\Jobs\SendMail;
use App\Models\Alert;
use App\Models\User;

class AlertRepository implements AlertContract
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

        $myProductSite = $product->productSites()->where("my_price", "y")->first();

        if ($alert->comparison_price_type == 'my price') {

            if (is_null($myProductSite)) {
                return false;
            }

            $comparisonPrice = $myProductSite->site->recent_price;


            /* the alert site is my site, no need to compare or notify */
        } else {
            $comparisonPrice = $alert->comparison_price;
        }

        if (is_null($comparisonPrice)) {
            return false;
        }

        $alertingProductSites = array();

        foreach ($productSites as $productSite) {

            $excludedProductSites = $alert->excludedProductSites;
            $excluded = false;
            foreach ($excludedProductSites as $excludedProductSite) {
                if ($excludedProductSite->site->getKey() == $productSite->site->getKey()) {
                    $excluded = true;
                }
            }
            if ($excluded) {
                continue;
            }

            /*TODO review necessity*/
            if ($productSite->site->status != 'ok') {
                continue;
            }

            if ($alert->comparison_price_type == 'my price' && $myProductSite->site->getKey() == $productSite->site->getKey()) {
                continue;
            }

            $alertUser = $this->comparePrices($productSite->site->recent_price, $comparisonPrice, $alert->operator);

            if ($alertUser) {
                $alertingProductSites[] = $productSite;
            }
        }

        if (count($alertingProductSites) == 0) {
            return false;
        }
        $emails = $alert->emails;
        foreach ($emails as $email) {
            dispatch((new SendMail('products.alert.email.product',
                compact(['alert', 'alertingProductSites', 'myProductSite']),
                array(
                    "email" => $email,
                    "subject" => 'SpotLite - Product Price Alert'
                )
            ))->onQueue("mailing"));
        }
    }

    public function triggerProductSiteAlert(Alert $alert)
    {
        $productSite = $alert->alertable;
        if (is_null($productSite)) {
            return false;
        }
        /*TODO review necessity*/
        if ($productSite->site->status != 'ok') {
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
            if ($myProductSite->site->getKey() == $site->getKey()) {
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
            $emails = $alert->emails;
            foreach ($emails as $email) {
                dispatch((new SendMail('products.alert.email.site',
                    compact(['alert', 'myProductSite']),
                    array(
                        "email" => $email->alert_email_address,
                        "subject" => 'SpotLite - Site Price Alert'
                    )))->onQueue("mailing"));
            }
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