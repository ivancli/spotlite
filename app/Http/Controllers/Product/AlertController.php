<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\AlertManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\ProductSiteManager;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\AlertEmail;
use App\Models\Site;
use App\Validators\Product\Alert\UpdateProductAlertValidator;
use App\Validators\Product\Alert\UpdateProductSiteAlertValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    protected $productManager;
    protected $alertManager;
    protected $productSiteManager;

    public function __construct(ProductManager $productManager, AlertManager $alertManager, ProductSiteManager $productSiteManager)
    {
        $this->productManager = $productManager;
        $this->alertManager = $alertManager;
        $this->productSiteManager = $productSiteManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        $product = $this->productManager->getProduct($product_id);
        $productSites = $product->productSites->pluck('site.site_url', 'product_site_id')->toArray();
        if (!is_null($product->alert)) {
            $emails = $product->alert->emails->pluck('alert_email_address', 'alert_email_address')->toArray();
            $excludedProductSites = $product->alert->excludedProductSites->pluck('site_id')->toArray();
        } else {
            $emails = array();
            $excludedProductSites = array();
        }
        return view('products.alert.product')->with(compact(['product', 'productSites', 'emails', 'excludedProductSites']));
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
        $product = $this->productManager->getProduct($product_id);
        if (is_null($product->alert)) {
            $alert = $this->alertManager->storeAlert($request->all());
        } else {
            $alert = $product->alert;
            $this->alertManager->updateAlert($alert->getKey(), $request->all());
            /*TODO enhance this part*/
            if (!$request->has('comparison_price')) {
                $alert->comparison_price = null;
                $alert->save();
            }
        }
        $alert->excludedProductSites()->detach();
        if ($request->has('site_id')) {
            foreach ($request->get('site_id') as $site) {
                $alert->excludedProductSites()->attach($site);
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
        $product = $this->productManager->getProduct($product_id);
        $alert = $product->alert;
        $alert->delete();
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
     * @param $product_site_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProductSiteAlert(Request $request, $product_site_id)
    {
        $productSite = $this->productSiteManager->getProductSite($product_site_id);

        if (!is_null($productSite->alert)) {
            $emails = $productSite->alert->emails->pluck('alert_email_address', 'alert_email_address')->toArray();
        } else {
            $emails = array();
        }

        return view('products.alert.site')->with(compact(['productSite', 'emails']));
    }

    /**
     * Update site alert
     *
     * @param UpdateProductSiteAlertValidator $updateProductSiteAlertValidator
     * @param Request $request
     * @param $product_site_id
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function updateProductSiteAlert(UpdateProductSiteAlertValidator $updateProductSiteAlertValidator, Request $request, $product_site_id)
    {
        try {
            $updateProductSiteAlertValidator->validate($request->all());
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

        if ($request->get('alert_owner_id') != $product_site_id) {
            abort(404);
            return false;
        }
        if ($request->get('alert_owner_type') != 'product_site') {
            abort(404);
            return false;
        }
        $productSite = $this->productSiteManager->getProductSite($product_site_id);
        if (is_null($productSite->alert)) {
            $alert = $this->alertManager->storeAlert($request->all());
        } else {
            $alert = $productSite->alert;
            $this->alertManager->updateAlert($alert->getKey(), $request->all());
            /*TODO enhance this part*/
            if (!$request->has('comparison_price')) {
                $alert->comparison_price = null;
                $alert->save();
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
     * Delete site alert
     *
     * @param Request $request
     * @param $product_site_id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function deleteProductSiteAlert(Request $request, $product_site_id)
    {
        $productSite = $this->productSiteManager->getProductSite($product_site_id);
        $alert = $productSite->alert;
        $alert->delete();
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
