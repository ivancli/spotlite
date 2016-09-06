<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\SiteManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    protected $siteManager;
    protected $productManager;

    public function __construct(SiteManager $siteManager, ProductManager $productManager)
    {
        $this->siteManager = $siteManager;
        $this->productManager = $productManager;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('product_id')) {
            $product = $this->productManager->getProduct($request->get('product_id'));
        }
        return view('products.site.create')->with(compact(['product']));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "site_url" => "required|url|max:2083"
        ]);
        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors()->all();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            $site = $this->siteManager->createSite($request->all());

            if ($request->has('product_id')) {
                $site->products()->attach($request->get('product_id'));
            }

            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'site']));
                } else {
                    return compact(['status', 'site']);
                }
            } else {
                return redirect()->route('product.index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $site = $this->siteManager->getSite($id);
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['site']));
            } else {
                return view('products.site.partials.single_site')->with(compact(['site']));
            }
        } else {
            return view('products.site.partials.single_site')->with(compact(['site']));
        }
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
}
