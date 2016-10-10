<?php

namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Alert\AlertContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Events\Products\Alert\AlertCreated;
use App\Events\Products\Alert\AlertCreateViewed;
use App\Events\Products\Alert\AlertCreating;
use App\Events\Products\Alert\AlertDeleted;
use App\Events\Products\Alert\AlertDeleting;
use App\Events\Products\Alert\AlertEdited;
use App\Events\Products\Alert\AlertEditing;
use App\Events\Products\Alert\AlertEditViewed;
use App\Events\Products\Alert\AlertListViewed;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\AlertEmail;
use App\Validators\Product\Alert\UpdateProductAlertValidator;
use App\Validators\Product\Alert\UpdateSiteAlertValidator;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /*repositories*/
    protected $productRepo;
    protected $alertRepo;
    protected $siteRepo;


    public function __construct(ProductContract $productContract, AlertContract $alertContract, SiteContract $siteContract)
    {
        $this->alertRepo = $alertContract;
        $this->productRepo = $productContract;
        $this->siteRepo = $siteContract;

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $alerts = $this->alertRepo->getDataTableAlerts();
            if ($request->wantsJson()) {
                return response()->json($alerts);
            } else {
                return $alerts;
            }
        } else {
            event(new AlertListViewed());
            return view('products.alert.index');
        }
    }

    /**
     * show edit category alert popup
     *
     * @param Request $request
     * @param $category_id
     */
    public function editCategoryAlert(Request $request, $category_id)
    {
        /*TODO implement this function*/
    }

    /**
     * Update category alert
     *
     * @param Request $request
     * @param $category_id
     */
    public function updateCategoryAlert(Request $request, $category_id)
    {

    }

    /**
     * Delete category alert
     *
     * @param Request $request
     * @param $category_id
     */
    public function deleteCategoryAlert(Request $request, $category_id)
    {

    }

    /**
     * show edit product alert popup
     *
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProductAlert(Request $request, $product_id)
    {
        $product = $this->productRepo->getProduct($product_id);
        $sites = $product->sites->pluck('site_url', 'site_id')->toArray();
        if (!is_null($product->alert)) {
            event(new AlertEditViewed($product->alert));
            $emails = $product->alert->emails->pluck('alert_email_address', 'alert_email_address')->toArray();
            $excludedSites = $product->alert->excludedSites->pluck('site_id')->toArray();
        } else {
            event(new AlertCreateViewed());
            $emails = array();
            $excludedSites = array();
        }
        return view('products.alert.product')->with(compact(['product', 'sites', 'emails', 'excludedSites']));
    }

    /**
     * Update product alert
     *
     * @param UpdateProductAlertValidator $updateProductAlertValidator
     * @param Request $request
     * @param $product_id
     * @return AlertController|bool|\Illuminate\Http\RedirectResponse
     */
    public function updateProductAlert(UpdateProductAlertValidator $updateProductAlertValidator, Request $request, $product_id)
    {
        try {
            $updateProductAlertValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        if ($request->get('alert_owner_id') != $product_id) {
            abort(404);
            return false;
        }
        if ($request->get('alert_owner_type') != 'product') {
            abort(404);
            return false;
        }
        $product = $this->productRepo->getProduct($product_id);
        if (is_null($product->alert)) {
            event(new AlertCreating());
            $alert = $this->alertRepo->storeAlert($request->all());
            event(new AlertCreated($alert));
        } else {
            $alert = $product->alert;

            event(new AlertEditing($alert));

            $this->alertRepo->updateAlert($alert->getKey(), $request->all());

            /*TODO enhance this part*/
            if (!$request->has('comparison_price')) {
                $alert->comparison_price = null;
                $alert->save();
            }

            event(new AlertEdited($alert));
        }

        /*TODO can use sync instead of detach attach*/
        $alert->excludedSites()->detach();
        if ($request->has('site_id')) {
            foreach ($request->get('site_id') as $site) {
                $alert->excludedSites()->attach($site);
            }
        }


        $alertEmails = array();
        foreach ($alert->emails as $email) {
            $email->delete();
        }
        if ($request->has('email')) {
            foreach ($request->get('email') as $email) {
                $alertEmail = AlertEmail::create(array(
                    "alert_id" => $alert->getKey(),
                    "alert_email_address" => $email
                ));
                $alertEmails[] = $alertEmail;
            }
        }
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'alert', 'alertEmails']));
            } else {
                return compact(['status', 'alert', 'alertEmails']);
            }
        } else {
            /*TODO implement this if needed*/
        }

    }

    /**
     * Delete product alert
     *
     * @param Request $request
     * @param $product_id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function deleteProductAlert(Request $request, $product_id)
    {
        $product = $this->productRepo->getProduct($product_id);
        $alert = $product->alert;

        event(new AlertDeleting($alert));

        $alert->delete();

        event(new AlertDeleted($alert));

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact('status'));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * show edit site alert popup
     *
     * @param Request $request
     * @param $site_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSiteAlert(Request $request, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);

        if (!is_null($site->alert)) {
            $emails = $site->alert->emails->pluck('alert_email_address', 'alert_email_address')->toArray();
            event(new AlertEditViewed($site->alert));
        } else {
            $emails = array();
            event(new AlertCreateViewed());
        }
        return view('products.alert.site')->with(compact(['site', 'emails']));
    }

    /**
     * Update site alert
     *
     * @param UpdateSiteAlertValidator $updateSiteAlertValidator
     * @param Request $request
     * @param $site_id
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function updateSiteAlert(UpdateSiteAlertValidator $updateSiteAlertValidator, Request $request, $site_id)
    {
        try {
            $updateSiteAlertValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        if ($request->get('alert_owner_id') != $site_id) {
            abort(404);
            return false;
        }
        if ($request->get('alert_owner_type') != 'site') {
            abort(404);
            return false;
        }
        $site = $this->siteRepo->getSite($site_id);
        if (is_null($site->alert)) {

            event(new AlertCreating());

            $alert = $this->alertRepo->storeAlert($request->all());

            event(new AlertCreated($alert));

        } else {
            $alert = $site->alert;

            event(new AlertEditing($alert));

            $this->alertRepo->updateAlert($alert->getKey(), $request->all());
            /*TODO enhance this part*/
            if (!$request->has('comparison_price')) {
                $alert->comparison_price = null;
                $alert->save();
            }

            event(new AlertEdited($alert));
        }

        $alertEmails = array();
        foreach ($alert->emails as $email) {
            $email->delete();
        }
        if ($request->has('email')) {
            foreach ($request->get('email') as $email) {
                $alertEmail = AlertEmail::create(array(
                    "alert_id" => $alert->getKey(),
                    "alert_email_address" => $email
                ));
                $alertEmails[] = $alertEmail;
            }
        }
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'alert', 'alertEmails']));
            } else {
                return compact(['status', 'alert', 'alertEmails']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * Delete site alert
     *
     * @param Request $request
     * @param $site_id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function deleteSiteAlert(Request $request, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);
        $alert = $site->alert;

        event(new AlertDeleting($alert));

        $alert->delete();

        event(new AlertDeleted($alert));

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact('status'));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }
}
