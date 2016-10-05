<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 9:15 AM
 */

namespace App\Repositories\Product\Alert;


use App\Contracts\Repository\Product\Alert\AlertContract;
use App\Events\Products\Alert\AlertSent;
use App\Events\Products\Alert\AlertTriggered;
use App\Filters\QueryFilter;
use App\Jobs\LogUserActivity;
use App\Jobs\SendMail;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;

class AlertRepository implements AlertContract
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
        event(new AlertTriggered($alert));
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
                    "email" => $email->alert_email_address,
                    "subject" => 'SpotLite - Product Price Alert'
                )
            ))->onQueue("mailing"));

            event(new AlertSent($alert, $email));
        }
    }

    public function triggerProductSiteAlert(Alert $alert)
    {
        event(new AlertTriggered($alert));

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

                event(new AlertSent($alert, $email));
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

    private function getAlertsCount()
    {
        $productSiteWithAlerts = auth()->user()->productSites()->with("alert")->get();

        $productSiteAlerts = array();
        foreach ($productSiteWithAlerts as $productSiteWithAlert) {
            if (!is_null($productSiteWithAlert->alert)) {
                $productSiteAlerts [] = $productSiteWithAlert->alert;
            }
        }

        return auth()->user()->productAlerts()->count() + count($productSiteAlerts);
    }

    public function getDataTableAlerts()
    {
        $productAlerts = $this->getProductAlertsByAuthUser();
        $productSiteAlerts = $this->getProductSiteAlertsByAuthUser();

        $alerts = $productAlerts->merge($productSiteAlerts);


        if ($this->request->has('order')) {
            foreach ($this->request->get('order') as $columnAndDirection) {
                if ($columnAndDirection['dir'] == 'asc') {
                    $alerts = $alerts->sortBy($columnAndDirection['column'])->values();
                } else {
                    $alerts = $alerts->sortByDesc($columnAndDirection['column'])->values();
                }
            }
        }

        if ($this->request->has('start')) {
            $alerts = $alerts->slice($this->request->get('start'), $alerts->count());
        }

        if ($this->request->has('length')) {
            $alerts = $alerts->take($this->request->get('length'));
        }

        if ($this->request->has('search') && isset($this->request->get('search')['value']) && strlen($this->request->get('search')['value']) > 0) {
            $searchString = $this->request->get('search')['value'];
            $alerts = $alerts->filter(function ($alert, $key) use ($searchString) {
                if (str_contains(strtolower($alert->alert_owner_type), strtolower($searchString))
                    || str_contains(strtolower($alert->comparison_price_type), strtolower($searchString))
                    || str_contains(strtolower($alert->comparison_price), strtolower($searchString))
                ) {
                    return true;
                }

                if ($alert->alert_owner_type == "product_site") {
                    return str_contains(strtolower($alert->alert_owner->site->domain), strtolower($searchString));
                } elseif ($alert->alert_owner_type == "product") {
                    return str_contains(strtolower($alert->alert_owner->product_name), strtolower($searchString));
                }
            })->values();
        }

        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getAlertsCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $alerts->count();
        } else {
            $output->recordsFiltered = $this->getAlertsCount();
        }
        $output->data = $alerts->toArray();
        return $output;
    }

    public function getProductAlertsByAuthUser()
    {
        return auth()->user()->productAlerts()->with('alertable')->get();
    }

    public function getProductSiteAlertsByAuthUser()
    {
        /*get product site with alerts*/
        $productSitesWithAlerts = auth()->user()->productSites()->with('alert')->with('alert.alertable')->with('alert.alertable.site')->get();

        $productSiteAlerts = $productSitesWithAlerts->pluck(['alert']);

        /*remove the null value*/
        $productSiteAlerts = $productSiteAlerts->reject(function ($alert, $key) {
            return is_null($alert);
        });
        return $productSiteAlerts;
    }
}