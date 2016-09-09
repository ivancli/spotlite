<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\AlertManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\AlertEmail;
use App\Models\Site;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    protected $productManager;
    protected $alertManager;

    public function __construct(ProductManager $productManager, AlertManager $alertManager)
    {
        $this->productManager = $productManager;
        $this->alertManager = $alertManager;
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
//        $productSites = $product->sites->pluck('site_url', 'site_id')->toArray();

        $emails = $product->alert->emails->pluck('alert_email_address', 'alert_email_address')->toArray();
        $excludedSites = $product->alert->excludedSites->pluck('site_id')->toArray();
        $alert = $product->alert;
        return view('products.alert.product')->with(compact(['product', 'alert', 'productSites', 'emails', 'excludedSites']));
    }

    /**
     * Update product alert
     *
     * @param Request $request
     * @param $product_id
     * @return AlertController|bool
     */
    public function updateProductAlert(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            "comparison_price_type" => "required",
            "operator" => "required",
            "comparison_price" => "required_if:comparison_price_type,specific price|numeric",
            "email" => "required|array"
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $status = false;
                $errors = $validator->errors()->all();
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
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
    }

    /**
     * show edit site alert popup
     *
     * @param Request $request
     * @param $product_site_id
     */
    public function editSiteAlert(Request $request, $product_site_id)
    {
        /*TODO implement this function*/
    }

    /**
     * Update site alert
     *
     * @param Request $request
     * @param $product_site_id
     */
    public function updateSiteAlert(Request $request, $product_site_id)
    {

    }
}
