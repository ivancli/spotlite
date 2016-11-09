<?php

namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Contracts\Repository\Product\Alert\AlertContract;
use App\Contracts\Repository\Product\Domain\DomainContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Events\Products\Site\SiteCreateViewed;
use App\Events\Products\Site\SiteDeleted;
use App\Events\Products\Site\SiteDeleting;
use App\Events\Products\Site\SiteEditViewed;
use App\Events\Products\Site\SiteMyPriceSet;
use App\Events\Products\Site\SitePricesViewed;
use App\Events\Products\Site\SiteSingleViewed;
use App\Events\Products\Site\SiteStored;
use App\Events\Products\Site\SiteStoring;
use App\Events\Products\Site\SiteUpdated;
use App\Events\Products\Site\SiteUpdating;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Models\Domain;
use App\Models\Site;
use App\Validators\Product\Site\GetPriceValidator;
use App\Validators\Product\Site\StoreValidator;
use App\Validators\Product\Site\UpdateValidator;
use Illuminate\Http\Request;

use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;
use Invigor\Crawler\Repositories\Crawlers\DefaultCrawler;
use Invigor\Crawler\Repositories\Parsers\XPathParser;

class SiteController extends Controller
{
    use CommonFunctions;

    protected $siteRepo;
    protected $domainRepo;
    protected $productRepo;
    protected $crawlerRepo;
    protected $alertRepo;


    public function __construct(SiteContract $siteContract, ProductContract $productContract, DomainContract $domainContract, CrawlerContract $crawlerContract, AlertContract $alertContract)
    {
        $this->siteRepo = $siteContract;
        $this->domainRepo = $domainContract;
        $this->productRepo = $productContract;
        $this->crawlerRepo = $crawlerContract;
        $this->alertRepo = $alertContract;
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
        if (!auth()->user()->canAddSite()) {
            $status = false;
            $errors = array("Please upgrade your subscription plan to add more sites");
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
            $site->comment = null;
            $site->save();
            /* adopt the historical prices of the copied site */
//            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $request->get('site_id'));
        } elseif ($request->has('domain_id')) {
            $targetDomain = $this->domainRepo->getDomain($request->get('domain_id'));
            $this->siteRepo->adoptDomainPreferences($site->getKey(), $request->get('domain_id'));
            $site->recent_price = $request->has('domain_price') ? $request->get('domain_price') : null;
            $site->last_crawled_at = null;
            $site->comment = null;
            $site->save();
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
     * @param CrawlerInterface $crawlerClass
     * @param ParserInterface $parserClass
     * @param $site_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request, CrawlerInterface $crawlerClass, ParserInterface $parserClass, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);
        $product = $site->product;


        $domainURL = parse_url($site->site_url)['host'];
        $domain = Domain::where("domain_url", $domainURL)->first();

        if (!is_null($domain->crawler_class)) {
            $crawlerClass = app()->make('Invigor\Crawler\Repositories\Crawlers\\' . $domain->crawler_class);
        }
        if (!is_null($domain->parser_class)) {
            $parserClass = app()->make('Invigor\Crawler\Repositories\Parsers\\' . $domain->parser_class);
        }

        if (!is_null($domain)) {
            $options = array(
                "url" => $site->site_url,
            );
            $content = $this->crawlerRepo->crawlPage($options, $crawlerClass);
            if (!is_null($content) && strlen($content) != 0) {
                for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
                    $xpath = $domain->preference->toArray()["xpath_{$xpathIndex}"];
                    if ($xpath != null || (!is_null($domain->crawler_class) || !is_null($domain->parser_class))) {
                        $result = $this->crawlerRepo->parserPrice($xpath, $content, $parserClass);
                        if (isset($result['status']) && $result['status'] == true) {
                            $price = $result['price'];
                            break;
                        } else {
                            if (isset($result['error'])) {
                                if ($result['error'] == "incorrect price") {
                                    continue;
                                } elseif ($result['error'] == "incorrect xpath") {
                                    continue;
                                }
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
        }
        if (isset($price) && $price > 0) {
            $targetDomain = array(
                "domain_id" => $domain->getKey(),
                "recent_price" => $price
            );
        }
        $sites = Site::where("site_url", $site->site_url)->whereNotNull("recent_price")->get();
        $sitePrices = array();
        if (!is_null($site->recent_price)) {
            $sitePrices[] = $site->recent_price;
        }
        foreach ($sites as $key => $otherSite) {
            if (!in_array($otherSite->recent_price, $sitePrices) && (!isset($targetDomain) || $targetDomain['recent_price'] != $otherSite->recent_price)) {
                $sitePrices[] = $otherSite->recent_price;
            } else {
                if ($site->getKey() != $otherSite->getKey()) {
                    unset($sites[$key]);
                }
            }
        }
        event(new SiteEditViewed($site));
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site', 'product', 'sites', 'targetDomain']));
            } else {
                return view('products.site.edit')->with(compact(['status', 'sites', 'site', 'targetDomain']));
            }
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
        event(new SiteUpdating($site));

        $site = $this->siteRepo->updateSite($site->getKey(), array("site_url" => $request->get('site_url'),));

        /** if user has chosen a price */
        if ($request->has('site_id')) {
            $targetSite = $this->siteRepo->getSite($request->get('site_id'));
            $this->siteRepo->adoptPreferences($site->getKey(), $request->get('site_id'));
            $site->recent_price = $targetSite->recent_price;
            $site->last_crawled_at = $targetSite->last_crawled_at;
            $site->comment = null;
            $site->save();
            /* adopt the historical prices of the copied site */
//            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $request->get('site_id'));
        } elseif ($request->has('domain_id')) {
            $targetDomain = $this->domainRepo->getDomain($request->get('domain_id'));
            $this->siteRepo->adoptDomainPreferences($id, $request->get('domain_id'));
            $site->recent_price = $request->has('domain_price') ? $request->get('domain_price') : null;
            $site->last_crawled_at = null;
            $site->comment = null;
            $site->save();
        } else {
            $this->siteRepo->clearPreferences($site->getKey());
            $site->recent_price = null;
            $site->last_crawled_at = null;
            $site->comment = $request->get('comment');
            $site->save();
        }
        $site->statusWaiting();
        event(new SiteUpdated($site));
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

    public function getPrices(GetPriceValidator $getPriceValidator, CrawlerInterface $crawlerClass, ParserInterface $parserClass, Request $request)
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

        $domainURL = parse_url($request->get('site_url'))['host'];
        $domain = Domain::where("domain_url", $domainURL)->first();
        if (!is_null($domain)) {
            if (!is_null($domain->crawler_class)) {
                $crawlerClass = app()->make('Invigor\Crawler\Repositories\Crawlers\\' . $domain->crawler_class);
            }
            if (!is_null($domain->parser_class)) {
                $parserClass = app()->make('Invigor\Crawler\Repositories\Parsers\\' . $domain->parser_class);
            }

            if (!is_null($domain)) {
                $options = array(
                    "url" => $request->get('site_url'),
                );
                $content = $this->crawlerRepo->crawlPage($options, $crawlerClass);
                if (!is_null($content) && strlen($content) != 0) {
                    for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
                        $xpath = $domain->preference->toArray()["xpath_{$xpathIndex}"];
                        if ($xpath != null || (!is_null($domain->crawler_class) || !is_null($domain->parser_class))) {
                            $result = $this->crawlerRepo->parserPrice($xpath, $content, $parserClass);
                            if (isset($result['status']) && $result['status'] == true) {
                                $price = $result['price'];
                                break;
                            } else {
                                if (isset($result['error'])) {
                                    if ($result['error'] == "incorrect price") {
                                        continue;
                                    } elseif ($result['error'] == "incorrect xpath") {
                                        continue;
                                    }
                                }
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
            if (isset($price) && $price > 0) {
                $targetDomain = array(
                    "domain_id" => $domain->getKey(),
                    "recent_price" => $price
                );
            }
        }

        $sites = Site::where("site_url", $request->get('site_url'))->whereNotNull("recent_price")->get();
//            $sites = $this->siteManager->getSiteByColumn('site_url', $request->get('site_url'));
        $sitePrices = array();
        foreach ($sites as $key => $site) {
            if (!in_array($site->recent_price, $sitePrices) && (!isset($targetDomain) || $targetDomain['recent_price'] != $site->recent_price)) {
                $sitePrices[] = $site->recent_price;
            } else {
                unset($sites[$key]);
            }
        }

        $status = true;
        event(new SitePricesViewed());
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'sites', 'targetDomain']));
            } else {
                return compact(['status', 'sites', 'targetDomain']);
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

            /*remove my price alert*/
            if (!is_null($site->alert) && $site->alert->comparison_price_type == "my price") {
                $this->alertRepo->deleteAlert($site->alert->getKey());
            }
        } else {
            $productMyPriceAlert = $site->product->alertOnMyPrice();
            if (!is_null($productMyPriceAlert)) {
                $this->alertRepo->deleteAlert($productMyPriceAlert->getKey());
            }
            $siteMyPriceAlerts = $site->product->siteAlertsOnMyPrice();
            foreach ($siteMyPriceAlerts as $siteMyPriceAlert) {
                $this->alertRepo->deleteAlert($siteMyPriceAlert->getKey());
            }
        }
        $site->my_price = $myPrice;
        $site->save();
        event(new SiteMyPriceSet($site));
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
        event(new SiteDeleting($site));
        $this->siteRepo->deleteSite($id);
        event(new SiteDeleted($site));
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
