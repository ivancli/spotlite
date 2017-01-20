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
use App\Models\Category;
use App\Models\Product;
use App\Models\Site;
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

    /**
     * An alert for all category
     *
     * @param Alert $alert
     */
    public function triggerUserAlert(Alert $alert)
    {
        event(new AlertTriggered($alert));
        $user = $alert->alertable;

        $categories = $user->categories;
        /*
         * TODO
         * There are two types of alert trigger
         *
         * 1. my price comparison
         * 2. any price changed
         *
         * gotta loop through each site to check if the price has been changed or not
         * gotta loop through each product to check
         * 1) if my price is set
         * 2) if my price is beaten
         *
         * */

        $priceChangedCategories = array();
        $priceBeatCategories = array();
        $priceBeatSpecificCategories = array();
        $excludedSites = $alert->excludedSites;
        foreach ($categories as $category) {
            $priceChangedCategories[$category->getKey()] = array();
            $priceBeatCategories[$category->getKey()] = array();
            $priceBeatSpecificCategories[$category->getKey()] = array();
            $priceBeatSpecificProducts = array();

            $products = $category->products;
            if (is_null($category->alert) || $category->alert->comparison_price_type != $alert->comparison_price_type) {
                foreach ($products as $product) {
                    if (is_null($product->alert) || $product->alert->comparison_price_type != $alert->comparison_price_type) {

                        $mySite = $product->sites()->where("my_price", "y")->first();
                        if ($alert->comparison_price_type == 'my price') {
                            $priceBeatCategories[$category->getKey()][$product->getKey()] = array();
                            if (is_null($mySite) || is_null($mySite->recent_price)) {
                                continue;
                            } else {
                                $comparisonPrice = $mySite->recent_price;
                                foreach ($product->sites as $site) {

                                    $excluded = false;
                                    foreach ($excludedSites as $excludedSite) {
                                        if ($excludedSite->getKey() == $site->getKey()) {
                                            $excluded = true;
                                        }
                                    }
                                    if ($excluded) {
                                        continue;
                                    } elseif ($site->status != 'ok') {
                                        continue;
                                    } elseif ($alert->comparison_price_type == 'my price' && $mySite->getKey() == $site->getKey()) {
                                        continue;
                                    } elseif ($this->comparePrices($site->recent_price, $comparisonPrice, "<")) {
                                        $priceBeatCategories[$category->getKey()][$product->getkey()][] = $site->getKey();
                                    }
                                }
                            }
                        } elseif ($alert->comparison_price_type == 'price changed') {
                            $priceChangedCategories[$category->getKey()][$product->getKey()] = array();
                            foreach ($product->sites as $site) {
                                $excluded = false;
                                foreach ($excludedSites as $excludedSite) {
                                    if ($excludedSite->getKey() == $site->getKey()) {
                                        $excluded = true;
                                    }
                                }
                                if ($excluded) {
                                    continue;
                                } elseif ($site->status != 'ok') {
                                    continue;
                                } elseif ($site->price_diff != 0) {
                                    $priceChangedCategories[$category->getKey()][$product->getKey()][] = $site->getKey();
                                }
                            }
                            /*A LIST OF SITE IDS WITH PRICE CHANGED*/
                        } elseif ($alert->comparison_price_type == 'specific price') {
                            $priceBeatSpecificCategories[$category->getKey()][$product->getKey()] = array();
                            $comparisonPrice = $alert->comparison_price;
                            foreach ($product->sites as $site) {
                                $excluded = false;
                                foreach ($excludedSites as $excludedSite) {
                                    if ($excludedSite->getKey() == $site->getKey()) {
                                        $excluded = true;
                                    }
                                }
                                if ($excluded) {
                                    continue;
                                } elseif ($site->status != 'ok') {
                                    continue;
                                } elseif ($this->comparePrices($site->recent_price, $comparisonPrice, "<")) {
                                    $priceBeatSpecificCategories[$category->getKey()][$product->getkey()][] = $site->getKey();
                                }
                            }
                        }
                    }


                }

            }
        }


        switch ($alert->comparison_price_type) {
            case "my price":
                if (count(array_flatten($priceBeatCategories)) == 0) {
                    return false;
                }
                $categoriesOfSites = $priceBeatCategories;
                break;
            case "price changed":
                if (count(array_flatten($priceChangedCategories)) == 0) {
                    return false;
                }
                $categoriesOfSites = $priceChangedCategories;
                break;
            case "specific price":
                if (count(array_flatten($priceBeatSpecificCategories)) == 0) {
                    return false;
                }
                $categoriesOfSites = $priceBeatSpecificCategories;
                break;
        }

        $payload = array();
        foreach ($categoriesOfSites as $categoryID => $products) {
            $category = Category::find($categoryID)->toArray();
            $category['products'] = array();
            foreach ($products as $productID => $sites) {
                if (!empty($sites)) {
                    $product = Product::findOrFail($productID)->toArray();
                    $product['sites'] = array();
                    foreach ($sites as $siteID) {
                        $site = Site::findOrFail($siteID)->toArray();
                        $product['sites'][] = $site;
                    }

                    $category['products'][] = $product;
                }
            }
            if (!empty($category['products'])) {
                $payload[] = $category;
            }
        }

        $emails = $alert->emails;
        foreach ($emails as $email) {
            dispatch((new SendMail('products.alert.email.user',
                compact(['alert', 'payload', 'mySite']),
                array(
                    "email" => $email->alert_email_address,
                    "subject" => 'SpotLite Price Alert',
                )
            ))->onQueue("mailing"));

            event(new AlertSent($alert, $email));
        }
        if ($alert->one_off == 'y') {
            dispatch((new DeleteObject($alert))->onQueue("deleting")->delay(300));
        }
    }

    /**
     * An alert for specific category
     *
     * @param Alert $alert
     * @return bool
     */
    public function triggerCategoryAlert(Alert $alert)
    {
        event(new AlertTriggered($alert));
        $category = $alert->alertable;
        $products = $category->products;

        $priceChangedProducts = array();
        $priceBeatProducts = array();
        $priceBeatSpecificProducts = array();
        $excludedSites = $alert->excludedSites;

        foreach ($products as $product) {
            /**IMPORTANT: if product has the same Comparison Price Type, it will not trigger Category Alert*/
            if (is_null($product->alert) || $product->alert->comparison_price_type != $alert->comparison_price_type) {
                $mySite = $product->sites()->where("my_price", "y")->first();
                if ($alert->comparison_price_type == 'my price') {
                    $priceBeatProducts[$product->getKey()] = array();
                    if (is_null($mySite) || is_null($mySite->recent_price)) {
                        continue;
                    } else {
                        $comparisonPrice = $mySite->recent_price;
                        foreach ($product->sites as $site) {

                            $excluded = false;
                            foreach ($excludedSites as $excludedSite) {
                                if ($excludedSite->getKey() == $site->getKey()) {
                                    $excluded = true;
                                }
                            }
                            if ($excluded) {
                                continue;
                            } elseif ($site->status != 'ok') {
                                continue;
                            } elseif ($alert->comparison_price_type == 'my price' && $mySite->getKey() == $site->getKey()) {
                                continue;
                            } elseif ($this->comparePrices($site->recent_price, $comparisonPrice, "<")) {
                                $priceBeatProducts[$product->getkey()][] = $site->getKey();
                            }
                        }
                    }
                } elseif ($alert->comparison_price_type == 'price changed') {
                    $priceChangedProducts[$product->getKey()] = array();
                    foreach ($product->sites as $site) {
                        $excluded = false;
                        foreach ($excludedSites as $excludedSite) {
                            if ($excludedSite->getKey() == $site->getKey()) {
                                $excluded = true;
                            }
                        }
                        if ($excluded) {
                            continue;
                        } elseif ($site->status != 'ok') {
                            continue;
                        } elseif ($site->price_diff != 0) {
                            $priceChangedProducts[$product->getKey()][] = $site->getKey();
                        }
                    }
                    /*A LIST OF SITE IDS WITH PRICE CHANGED*/
                } elseif ($alert->comparison_price_type == 'specific price') {
                    $priceBeatSpecificProducts[$product->getKey()] = array();
                    $comparisonPrice = $alert->comparison_price;
                    foreach ($product->sites as $site) {
                        $excluded = false;
                        foreach ($excludedSites as $excludedSite) {
                            if ($excludedSite->getKey() == $site->getKey()) {
                                $excluded = true;
                            }
                        }
                        if ($excluded) {
                            continue;
                        } elseif ($site->status != 'ok') {
                            continue;
                        } elseif ($this->comparePrices($site->recent_price, $comparisonPrice, "<")) {
                            $priceBeatSpecificProducts[$product->getkey()][] = $site->getKey();
                        }
                    }
                }
            }

            /*$priceBeatProducts a list of site ids which have beaten my price*/
            /*$priceChangedProducts a list of site ids which have changed prices*/
            /*$priceBeatSpecificProducts a list of site ids which have beaten a specific price*/
        }
        switch ($alert->comparison_price_type) {
            case "my price":
                if (count(array_flatten($priceBeatProducts)) == 0) {
                    return false;
                }
                $productsOfSites = $priceBeatProducts;
                break;
            case "price changed":
                if (count(array_flatten($priceChangedProducts)) == 0) {
                    return false;
                }
                $productsOfSites = $priceChangedProducts;
                break;
            case "specific price":
                if (count(array_flatten($priceBeatSpecificProducts)) == 0) {
                    return false;
                }
                $productsOfSites = $priceBeatSpecificProducts;
                break;
        }


        $payload = array();
        foreach ($productsOfSites as $productID => $sites) {
            if (!empty($sites)) {
                $product = Product::findOrFail($productID)->toArray();
                $product['sites'] = array();
                foreach ($sites as $siteID) {
                    $site = Site::findOrFail($siteID)->toArray();
                    $product['sites'][] = $site;
                }

                $payload[] = $product;
            }
        }

        $emails = $alert->emails;
        foreach ($emails as $email) {
            dispatch((new SendMail('products.alert.email.category',
                compact(['alert', 'payload', 'mySite']),
                array(
                    "email" => $email->alert_email_address,
                    "subject" => 'SpotLite Price Alert ' . $category->category_name,
                )
            ))->onQueue("mailing"));

            event(new AlertSent($alert, $email));
        }
        if ($alert->one_off == 'y') {
            dispatch((new DeleteObject($alert))->onQueue("deleting")->delay(300));
        }
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
        } elseif ($alert->comparison_price_type == "specific price") {
            $comparisonPrice = $alert->comparison_price;
        }
        if ($alert->comparison_price_type != "price changed" && (!isset($comparisonPrice) || is_null($comparisonPrice))) {
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
            switch ($alert->comparison_price_type) {
                case "price changed":
                    $alertUser = true;
                    break;
                default:
                    $alertUser = $this->comparePrices($site->recent_price, $comparisonPrice, $alert->operator);
            }

            if ($alertUser && $site->price_diff != 0) {
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
        if ($alertUser && $site->price_diff != 0) {
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

        return auth()->user()->alerts()->count() + auth()->user()->categoryAlerts()->count() + auth()->user()->productAlerts()->count() + count($siteAlerts);
    }

    public function getDataTableAlerts()
    {
        $userAlerts = $this->getUserAlertsByAuthUser();
        $categoryAlerts = $this->getCategoryAlertsByAuthUser();
        $productAlerts = $this->getProductAlertsByAuthUser();
        $siteAlerts = $this->getSiteAlertsByAuthUser();

        $alerts = $categoryAlerts->merge($userAlerts);
        $alerts = $alerts->merge($productAlerts);
        $alerts = $alerts->merge($siteAlerts);


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
                } elseif ($alert->alert_owner_type == "category") {
                    return str_contains(strtolower($alert->alert_owner->category_name), strtolower($searchString));
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

    public function getCategoryAlertsByAuthUser()
    {
        return auth()->user()->categoryAlerts()->with('alertable')->get();
    }

    public function getUserAlertsByAuthUser()
    {
        return auth()->user()->alerts()->with('alertable')->get();
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