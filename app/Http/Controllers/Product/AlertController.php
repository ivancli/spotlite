<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\ProductManager;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

use App\Http\Requests;

class AlertController extends Controller
{
    protected $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
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
        $alert = $product->alert;
        return view('products.alert.product')->with(compact(['product', 'alert']));
    }

    /**
     * Update product alert
     *
     * @param Request $request
     * @param $product_id
     */
    public function updateProductAlert(Request $request, $product_id)
    {
        $alert = Alert::findOrFail(1);
        dump($alert->product);
//        $product = $this->productManager->getProduct($product_id);
//        dump($product->alert);
//        dump($product);
//        dd($request->all());
    }

    /**
     * show edit site alert popup
     *
     * @param Request $request
     * @param $site_id
     */
    public function editSiteAlert(Request $request, $site_id)
    {
        /*TODO implement this function*/
    }

    /**
     * Update site alert
     *
     * @param Request $request
     * @param $site_id
     */
    public function updateSiteAlert(Request $request, $site_id)
    {

    }
}
