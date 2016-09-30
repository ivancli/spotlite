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
}
