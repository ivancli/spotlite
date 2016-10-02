<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 11:52 AM
 */

namespace App\Http\Controllers\Product;


use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\ProductSite\ProductSiteContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Events\Products\Site\SiteCreateViewed;
use App\Events\Products\Site\SiteDetached;
use App\Events\Products\Site\SiteEditViewed;
use App\Events\Products\Site\SiteSingleViewed;
use App\Events\Products\Site\SiteStored;
use App\Events\Products\Site\SiteStoring;
use App\Events\Products\Site\SiteUpdating;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Models\ProductSite;
use App\Models\Site;
use App\Validators\Product\ProductSite\StoreProductSiteValidator;
use App\Validators\Product\ProductSite\UpdateProductSiteValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSiteController extends Controller
{
    use CommonFunctions;

    protected $siteRepo;
    protected $productRepo;
    protected $productSiteRepo;

    public function __construct(SiteContract $siteContract, ProductContract $productContract, ProductSiteContract $productSiteContract)
    {
        $this->siteRepo = $siteContract;
        $this->productRepo = $productContract;
        $this->productSiteRepo = $productSiteContract;
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
     * @param StoreProductSiteValidator $storeProductSiteValidator
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreProductSiteValidator $storeProductSiteValidator, Request $request)
    {
        try {
            $storeProductSiteValidator->validate($request->all());
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
            $productSite = ProductSite::create(array(
                "site_id" => $request->get('site_id'),
                "product_id" => $request->get('product_id')
            ));
        } else {
            $input = $request->all();
            $input['site_url'] = $this->removeGlobalWebTracking($input['site_url']);

            $site = $this->siteRepo->createSite($input);
            event(new SiteStored($site));

            $productSite = ProductSite::create(array(
                "site_id" => $site->getKey(),
                "product_id" => $request->get('product_id')
            ));
        }
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'productSite']));
            } else {
                return compact(['status', 'productSite']);
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
        $productSite = $this->productSiteRepo->getProductSite($id);
//        $product = $productSite->product;
//        $site = $productSite->site;
//        if ($request->has('product_id')) {
//            dump($site->products()->wherePivot("product_id", $request->get('product_id'))->get()->toArray());
//        }
//        event(new SiteSingleViewed($site));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['productSite']));
            } else {
                return view('products.site.partials.single_site')->with(compact(['productSite']));
            }
        } else {
            return view('products.site.partials.single_site')->with(compact(['productSite']));
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
        $productSite = $this->productSiteRepo->getProductSite($id);
        $product = $productSite->product;
        $site = $productSite->site;
        event(new SiteEditViewed($site));
        $sites = Site::where("site_url", $site->site_url)->whereNotNull("recent_price")->get();
//        $sites = $this->siteManager->getSiteByColumn('site_url', $site->site_url);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site', 'product', 'sites']));
            } else {
                return view('products.site.edit')->with(compact(['status', 'sites', 'productSite']));
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductSiteValidator $updateProductSiteValidator
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateProductSiteValidator $updateProductSiteValidator, Request $request, $id)
    {
        try {
            $updateProductSiteValidator->validate($request->all());
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
        $productSite = $this->productSiteRepo->getProductSite($id);
        $originalSite = $productSite->site;
//        $oldProduct = $productSite->product;

        /** if user has chosen a price */
        if ($request->has('site_id')) {
            $newSite = $this->siteRepo->getSite($request->get('site_id'));
        } elseif ($request->has('site_url')) {
            /** if user has provide a url */
            $site_url = $this->removeGlobalWebTracking($request->get('site_url'));
            $newSite = $this->siteRepo->createSite(array(
                "site_url" => $site_url,
            ));
        }
        if (isset($newSite)) {
            event(new SiteUpdating($newSite));
            /** if the original website is different from the one user provided */
            if ($originalSite->getKey() != $newSite->getKey()) {
                $productSite->site_id = $newSite->getKey();
                $productSite->save();
            }
            $status = true;
        }
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'productSite']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    public function setMyPrice(Request $request, $product_site_id)
    {
        /*TODO validate my price from request*/

        $productSite = $this->productSiteRepo->getProductSite($product_site_id);
        $myPrice = $request->get("my_price");
        if ($myPrice == "y") {
            $allProductSitesOfThisProduct = $productSite->product->productSites;
            foreach ($allProductSitesOfThisProduct as $otherProductSite) {
                $otherProductSite->my_price = "n";
                $otherProductSite->save();
            }
        }
        $productSite->my_price = $myPrice;
        $productSite->save();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'productSite']));
            } else {
                return compact(['status', 'productSite']);
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
        $productSite = $this->productSiteRepo->getProductSite($id);
        $product = $productSite->product;
        $site = $productSite->site;
        $productSite = $this->productSiteRepo->deleteProductSite($id);
        event(new SiteDetached($site, $product));
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