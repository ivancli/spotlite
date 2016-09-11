<?php

namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\ProductManager;
use App\Contracts\ProductManagement\SiteManager;
use App\Events\Products\Site\SiteAttached;
use App\Events\Products\Site\SiteCreateViewed;
use App\Events\Products\Site\SiteDetached;
use App\Events\Products\Site\SiteEditViewed;
use App\Events\Products\Site\SitePricesViewed;
use App\Events\Products\Site\SiteSingleViewed;
use App\Events\Products\Site\SiteStored;
use App\Events\Products\Site\SiteStoring;
use App\Events\Products\Site\SiteUpdated;
use App\Events\Products\Site\SiteUpdating;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Validators\Product\Site\GetPriceValidator;
use App\Validators\Product\Site\StoreValidator;
use App\Validators\Product\Site\UpdateValidator;
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

    public function getPrices(GetPriceValidator $getPriceValidator, Request $request)
    {
        try {
            $getPriceValidator->validate($request->all());
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

        $sites = Site::where("site_url", $request->get('site_url'))->whereNotNull("recent_price")->get();
//            $sites = $this->siteManager->getSiteByColumn('site_url', $request->get('site_url'));
        $status = true;
        event(new SitePricesViewed());
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

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->has('product_id')) {
            $product = $this->productManager->getProduct($request->get('product_id'));
        }
        event(new SiteCreateViewed());
        return view('products.site.create')->with(compact(['product']));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator, Request $request)
    {

        try {
            $storeValidator->validate($request->all());
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

        event(new SiteStoring());
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
        event(new SiteStored($site));
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

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        /* TODO there is yet no way to get around with this, unable to get last attached product_site_id */

        $site = $this->siteManager->getSite($id);
//        if ($request->has('product_id')) {
//            dump($site->products()->wherePivot("product_id", $request->get('product_id'))->get()->toArray());
//        }
        event(new SiteSingleViewed($site));
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {

        $product_site_id = $request->get('product_site_id');
        $site = $this->siteManager->getSite($id);
        event(new SiteEditViewed($site));
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
     * @param UpdateValidator $updateValidator
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateValidator $updateValidator, Request $request, $id)
    {
        try {
            $updateValidator->validate($request->all());
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
        if (isset($newSite)) {
            event(new SiteUpdating($newSite));
            if ($originalSite->getKey() != $newSite->getKey()) {
                $oldProduct = $originalSite->products()->wherePivot("product_site_id", $product_site_id)->first();
                $originalSite->products()->wherePivot("product_site_id", $product_site_id)->detach();
                event(new SiteDetached($originalSite, $oldProduct));
                $newSite->products()->attach($product->getKey());
                event(new SiteAttached($newSite, $product));
            }
            $status = true;
            event(new SiteUpdated($newSite));
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
            $oldProduct = $site->products()->wherePivot("product_site_id", $request->get('product_site_id'))->first();
            $site->products()->wherePivot("product_site_id", $request->get('product_site_id'))->detach();
            event(new SiteDetached($site, $oldProduct));
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
