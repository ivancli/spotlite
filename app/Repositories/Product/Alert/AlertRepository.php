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
use App\Jobs\DeleteObject;
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
        if (!isset($options['one_off'])) {
            $options['one_off'] = 'n';
        }
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

        $sites = $product->sites;

        $mySite = $product->sites()->where("my_price", "y")->first();

        if ($alert->comparison_price_type == 'my price') {

            if (is_null($mySite)) {
                return false;
            }

            $comparisonPrice = $mySite->recent_price;


            /* the alert site is my site, no need to compare or notify */
        } else {
            $comparisonPrice = $alert->comparison_price;
        }

        if (is_null($comparisonPrice)) {
            return false;
        }

        $alertingSites = array();

        foreach ($sites as $site) {

            $excludedSites = $alert->excludedSites;
            $excluded = false;
            foreach ($excludedSites as $excludedSite) {
                if ($excludedSite->getKey() == $site->getKey()) {
                    $excluded = true;
                }
            }
            if ($excluded) {
                continue;
            }

            /*TODO review necessity*/
            if ($site->status != 'ok') {
                continue;
            }

            if ($alert->comparison_price_type == 'my price' && $mySite->getKey() == $site->getKey()) {
                continue;
            }

            $alertUser = $this->comparePrices($site->recent_price, $comparisonPrice, $alert->operator);

            if ($alertUser) {
                $alertingSites[] = $site;
            }
        }

        if (count($alertingSites) == 0) {
            return false;
        }
        $emails = $alert->emails;
        foreach ($emails as $email) {
            dispatch((new SendMail('products.alert.email.product',
                compact(['alert', 'alertingSites', 'mySite']),
                array(
                    "email" => $email->alert_email_address,
                    "subject" => 'SpotLite Price Alert ' . $product->product_name
                )
            ))->onQueue("mailing"));

            event(new AlertSent($alert, $email));
        }
        if ($alert->one_off == 'y') {
            dispatch((new DeleteObject($alert))->onQueue("deleting")->delay(300));
//            $this->deleteAlert($alert->getKey());
        }
    }

    public function triggerSiteAlert(Alert $alert)
    {
        event(new AlertTriggered($alert));

        $site = $alert->alertable;
        if (is_null($site)) {
            return false;
        }
        /*TODO review necessity*/
        if ($site->status != 'ok') {
            return false;
        }
        $product = $site->product;

        $mySite = $product->sites()->where("my_price", "y")->first();

        if ($alert->comparison_price_type == 'my price') {
            if (is_null($mySite)) {
                return false;
            }
            /* the alert site is my site, no need to compare or notify */
            if ($mySite->getKey() == $site->getKey()) {
                return false;
            }
            $comparisonPrice = $mySite->recent_price;
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
                    compact(['alert', 'mySite']),
                    array(
                        "email" => $email->alert_email_address,
                        "subject" => 'SpotLite Price Alert ' . $site->product->product_name
                    )))->onQueue("mailing"));
                event(new AlertSent($alert, $email));
            }

            if ($alert->one_off == 'y') {
                dispatch((new DeleteObject($alert))->onQueue("deleting")->delay(300));
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
        $siteWithAlerts = auth()->user()->sites()->with("alert")->get();

        $siteAlerts = array();
        foreach ($siteWithAlerts as $siteWithAlert) {
            if (!is_null($siteWithAlert->alert)) {
                $siteAlerts [] = $siteWithAlert->alert;
            }
        }

        return auth()->user()->productAlerts()->count() + count($siteAlerts);
    }

    public function getDataTableAlerts()
    {
        $productAlerts = $this->getProductAlertsByAuthUser();
        $siteAlerts = $this->getSiteAlertsByAuthUser();

        $alerts = $productAlerts->merge($siteAlerts);


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

                if ($alert->alert_owner_type == "site") {
                    return str_contains(strtolower($alert->alert_owner->domain), strtolower($searchString));
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

    public function getSiteAlertsByAuthUser()
    {
        /*get product site with alerts*/
        $sitesWithAlerts = auth()->user()->sites()->with('alert')->with('alert.alertable')->get();

        $siteAlerts = $sitesWithAlerts->pluck(['alert']);

        /*remove the null value*/
        $siteAlerts = $siteAlerts->reject(function ($alert, $key) {
            return is_null($alert);
        });
        return $siteAlerts;
    }
}