<?php

namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Events\Products\Site\SiteCreateViewed;
use App\Events\Products\Site\SitePricesViewed;
use App\Events\Products\Site\SiteStored;
use App\Events\Products\Site\SiteStoring;
use App\Events\Products\Site\SiteUpdating;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Models\Site;
use App\Validators\Product\Site\GetPriceValidator;
use App\Validators\Product\Site\StoreValidator;
use App\Validators\Product\Site\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    use CommonFunctions;

    protected $siteRepo;
    protected $productRepo;

    public function __construct(SiteContract $siteContract, ProductContract $productContract)
    {
        $this->siteRepo = $siteContract;
        $this->productRepo = $productContract;
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
        $site = $this->siteRepo->getSite($id);
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
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->has('product_id')) {
            $product = $this->productRepo->getProduct($request->get('product_id'));
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
        $input = $request->all();
        $site = $this->siteRepo->createSite($input);


        /** if user has chosen a price */
        if ($request->has('site_id')) {
            $targetSite = $this->siteRepo->getSite($request->get('site_id'));
            $this->siteRepo->adoptPreferences($site->getKey(), $request->get('site_id'));
            $site->recent_price = $targetSite->recent_price;
            $site->last_crawled_at = $targetSite->last_crawled_at;
            $site->save();
            /* adopt the historical prices of the copied site */
            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $request->get('site_id'));
        } else {
            $this->siteRepo->clearPreferences($site->getKey());
            $site->recent_price = null;
            $site->last_crawled_at = null;
            $site->save();
        }


        event(new SiteStored($site));

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


    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $site = $this->siteRepo->getSite($id);
        $product = $site->product;
        $sites = Site::where("site_url", $site->site_url)->whereNotNull("recent_price")->get();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site', 'product', 'sites']));
            } else {
                return view('products.site.edit')->with(compact(['status', 'sites', 'site']));
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
        $site = $this->siteRepo->getSite($id);

        $site = $this->siteRepo->updateSite($site->getKey(), array("site_url" => $request->get('site_url'),));

        /** if user has chosen a price */
        if ($request->has('site_id')) {
            $targetSite = $this->siteRepo->getSite($request->get('site_id'));
            $this->siteRepo->adoptPreferences($site->getKey(), $request->get('site_id'));
            $site->recent_price = $targetSite->recent_price;
            $site->last_crawled_at = $targetSite->last_crawled_at;
            $site->save();
            /* adopt the historical prices of the copied site */
            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $request->get('site_id'));
        } else {
            $this->siteRepo->clearPreferences($site->getKey());
            $site->recent_price = null;
            $site->last_crawled_at = null;
            $site->save();
        }
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
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

    public function setMyPrice(Request $request, $site_id)
    {
        /*TODO validate my price from request*/

        $site = $this->siteRepo->getSite($site_id);
        $myPrice = $request->get("my_price");
        if ($myPrice == "y") {
            $allSitesOfThisProduct = $site->product->sites;
            foreach ($allSitesOfThisProduct as $allOtherSites) {
                $allOtherSites->my_price = "n";
                $allOtherSites->save();
            }
        }
        $site->my_price = $myPrice;
        $site->save();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status', 'site']);
            }
        } else {
            /*TODO implement this if needed*/
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
        $site = $this->siteRepo->getSite($id);
        $this->siteRepo->deleteSite($id);
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
    }
}
