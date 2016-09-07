<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\SiteManager;
use App\Http\Controllers\Controller;
use App\Models\Site;
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    public function getPrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "site_url" => "required|url|max:2083"
        ]);
        if ($validator->fails()) {
            $status = false;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return compact(['status']);
                }
            } else {
                //TODO implement if needed
            }
        } else {

            $sites = Site::where("site_url", $request->get('site_url'))->whereNotNull("recent_price")->get();
//            $sites = $this->siteManager->getSiteByColumn('site_url', $request->get('site_url'));
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'sites']));
                } else {
                    return compact(['status', 'sites']);
                }
            } else {
                //TODO implement if needed
            }
        }
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
            if ($request->has('site_id')) {
                $site = $this->siteManager->getSite($request->get('site_id'));
                if ($request->has('product_id')) {
                    $site->products()->attach($request->get('product_id'));
                }
            } else {
                $site = $this->siteManager->createSite($request->all());

                if ($request->has('product_id')) {
                    $site->products()->attach($request->get('product_id'));
                }
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
        /* TODO there is yet no way to get around with this, unable to get last attached product_site_id */

        $site = $this->siteManager->getSite($id);
//        if ($request->has('product_id')) {
//            dump($site->products()->wherePivot("product_id", $request->get('product_id'))->get()->toArray());
//        }
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $product_site_id = $request->get('product_site_id');
        $site = $this->siteManager->getSite($id);
        $product = $site->products()->wherePivot("product_site_id", $product_site_id)->first();
        $sites = Site::where("site_url", $site->site_url)->whereNotNull("recent_price")->get();
//        $sites = $this->siteManager->getSiteByColumn('site_url', $site->site_url);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site', 'product', 'sites']));
            } else {
                return view('products.site.edit')->with(compact(['status', 'site', 'product', 'sites', 'product_site_id']));
            }
        } else {
            /*TODO implement this if necessary*/
        }
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
        $validator = Validator::make($request->all(), [
            "product_id" => "required",
            "product_site_id" => "required"
        ]);
        if ($validator->fails()) {

        } else {
            $originalSite = $this->siteManager->getSite($id);
            $product_id = $request->get('product_id');
            $product = $this->productManager->getProduct($product_id);
            $product_site_id = $request->get('product_site_id');
            if ($request->has('site_id')) {
                $newSite = $this->siteManager->getSite($request->get('site_id'));
            } elseif ($request->has('site_url')) {
                $newSite = $this->siteManager->createSite(array(
                    "site_url" => $request->get('site_url'),
                ));
            }

            if ($originalSite->getKey() != $newSite->getKey()) {
                $originalSite->products()->wherePivot("product_site_id", $product_site_id)->detach();
                $newSite->products()->attach($product->getKey());
            }
            $status = true;
        }
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $site = $this->siteManager->getSite($id);
        if ($request->has('product_site_id')) {
            $site->products()->wherePivot("product_site_id", $request->get('product_site_id'))->detach();
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return compact(['status']);
                }
            } else {
                return redirect()->route('product.index');
            }
        } else {
            $status = false;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return compact(['status']);
                }
            } else {
                return redirect()->back()->withInput();
            }
        }

    }
}
