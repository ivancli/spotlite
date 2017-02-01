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
    protected $request;


    public function __construct(SiteContract $siteContract, ProductContract $productContract, DomainContract $domainContract, CrawlerContract $crawlerContract, AlertContract $alertContract, Request $request)
    {
        $this->middleware('permission:create_site', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_site', ['only' => ['show']]);
        $this->middleware('permission:reorder_site', ['only' => ['updateOrder']]);
        $this->middleware('permission:update_site', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_site', ['only' => ['destroy']]);
        $this->middleware('permission:get_site_price', ['only' => ['getPrices']]);
        $this->middleware('permission:set_my_price', ['only' => ['setMyPrice']]);

        $this->siteRepo = $siteContract;
        $this->domainRepo = $domainContract;
        $this->productRepo = $productContract;
        $this->crawlerRepo = $crawlerContract;
        $this->alertRepo = $alertContract;
        $this->request = $request;
    }


    public function indexByProduct($product_id)
    {
        $product = $this->productRepo->getProduct($product_id);
        $sites = $this->siteRepo->getSitesByProduct($product);
        $html = "";
        foreach ($sites as $site) {
            $html .= view('products.site.partials.single_site')->with(compact(['site']));
        }
        $status = true;
        if ($this->request->wantsJson()) {
            return response()->json(compact(['html', 'status']));
        } else {
            return $html;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        /* TODO there is yet no way to get around with this, unable to get last attached product_site_id */
        $site = $this->siteRepo->getSite($id);

        event(new SiteSingleViewed($site));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if ($this->request->has('product_id')) {
            $product = $this->productRepo->getProduct($this->request->get('product_id'));
        }
        event(new SiteCreateViewed());
        return view('products.site.create')->with(compact(['product']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator)
    {
        $user = auth()->user();
        if ($user->needSubscription) {
            $criteria = auth()->user()->subscriptionCriteria();
            if (isset($criteria->site) && $criteria->site != 0) {
                $productId = $this->request->get('product_id');
                $product = $this->productRepo->getProduct($productId);
                $currentSites = $product->sites()->count();
                $maxSites = $criteria->site;
                if ($currentSites >= $maxSites) {
                    $status = false;
                    $errors = array("Please upgrade your subscription plan to add more sites");
                    if ($this->request->ajax()) {
                        if ($this->request->wantsJson()) {
                            return response()->json(compact(['status', 'errors']));
                        } else {
                            return compact(['status', 'errors']);
                        }
                    } else {
                        return redirect()->back()->withInput()->withErrors($errors);
                    }
                }
            }
        }

        $storeValidator->validate($this->request->all());
        event(new SiteStoring());
        $input = $this->request->all();
        $site = $this->siteRepo->createSite($input);

        /** if user has chosen a price */
        if ($this->request->has('site_id')) {
            $targetSite = $this->siteRepo->getSite($this->request->get('site_id'));
            $this->siteRepo->adoptPreferences($site->getKey(), $this->request->get('site_id'));
            $site->recent_price = $targetSite->recent_price;
            $site->last_crawled_at = $targetSite->last_crawled_at;
            $site->comment = null;
            $site->save();
            /* adopt the historical prices of the copied site */
//            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $this->request->get('site_id'));
        } elseif ($this->request->has('domain_id')) {
            $targetDomain = $this->domainRepo->getDomain($this->request->get('domain_id'));
            $this->siteRepo->adoptDomainPreferences($site->getKey(), $this->request->get('domain_id'));
            $site->recent_price = $this->request->has('domain_price') ? $this->request->get('domain_price') : null;
            $site->last_crawled_at = null;
            $site->comment = null;
            $site->save();
        } else {
            $this->siteRepo->clearPreferences($site->getKey());
            $site->recent_price = null;
            $site->last_crawled_at = null;
            $site->save();
        }

        /*set my price*/
        $companyURL = auth()->user()->company_url;
        if (!is_null($companyURL) && !empty($companyURL)) {
            $siteDomain = parse_url($site->site_url)['host'];
            $myCompanyDomain = parse_url($companyURL)['host'];

            list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
            list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);

            //matching both sub-domain and domain
            if ($subdomainSplitted == $domainSplitted) {
//            if ($siteDomain == $myCompanyDomain) { // match exact sub-domain
                $hasMyPrice = false;
                foreach ($site->product->sites as $eachSite) {
                    if (!is_null($eachSite->my_price) && $eachSite->my_price == 'y') {
                        $hasMyPrice = true;
                    }
                }
                if ($hasMyPrice == false) {
                    $site->my_price = 'y';
                    $site->save();
                }
            }
        }

        event(new SiteStored($site));

        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
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
     * @param CrawlerInterface $crawlerClass
     * @param ParserInterface $parserClass
     * @param $site_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(CrawlerInterface $crawlerClass, ParserInterface $parserClass, $site_id)
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
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
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
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateValidator $updateValidator, $id)
    {
        $updateValidator->validate($this->request->all());
        $site = $this->siteRepo->getSite($id);
        event(new SiteUpdating($site));

        $site = $this->siteRepo->updateSite($site->getKey(), array("site_url" => $this->request->get('site_url'),));

        /** if user has chosen a price */
        if ($this->request->has('site_id')) {
            $targetSite = $this->siteRepo->getSite($this->request->get('site_id'));
            $this->siteRepo->adoptPreferences($site->getKey(), $this->request->get('site_id'));
            $site->recent_price = $targetSite->recent_price;
            $site->last_crawled_at = $targetSite->last_crawled_at;
            $site->comment = null;
            $site->save();
            /* adopt the historical prices of the copied site */
//            $this->siteRepo->copySiteHistoricalPrice($site->getKey(), $this->request->get('site_id'));
        } elseif ($this->request->has('domain_id')) {
            $targetDomain = $this->domainRepo->getDomain($this->request->get('domain_id'));
            $this->siteRepo->adoptDomainPreferences($id, $this->request->get('domain_id'));
            $site->recent_price = $this->request->has('domain_price') ? $this->request->get('domain_price') : null;
            $site->last_crawled_at = null;
            $site->comment = null;
            $site->save();
        } else {
            $this->siteRepo->clearPreferences($site->getKey());
            $site->recent_price = null;
            $site->last_crawled_at = null;
            $site->comment = $this->request->get('comment');
            $site->save();
        }
        $site->statusWaiting();


        /*set my price*/
        $companyURL = auth()->user()->company_url;
        if (!is_null($companyURL) && !empty($companyURL)) {
            $siteDomain = parse_url($site->site_url)['host'];
            $myCompanyDomain = parse_url($companyURL)['host'];

            list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
            list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);

            //matching both sub-domain and domain
            if ($subdomainSplitted == $domainSplitted) {
//            if ($siteDomain == $myCompanyDomain) { // match exact sub-domain
                $hasMyPrice = false;
                foreach ($site->product->sites as $eachSite) {
                    if (!is_null($eachSite->my_price) && $eachSite->my_price == 'y') {
                        $hasMyPrice = true;
                    }
                }
                if ($hasMyPrice == false) {
                    $site->my_price = 'y';
                    $site->save();
                }
            }
        }


        event(new SiteUpdated($site));
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    public function getPrices(GetPriceValidator $getPriceValidator, CrawlerInterface $crawlerClass, ParserInterface $parserClass)
    {
        $getPriceValidator->validate($this->request->all());
        $domainURL = parse_url($this->request->get('site_url'))['host'];
        $domain = Domain::where("domain_url", $domainURL)->first();
        if (!is_null($domain)) {
            if (!is_null($domain->crawler_class)) {
                $crawlerClass = app()->make('Invigor\Crawler\Repositories\Crawlers\\' . $domain->crawler_class);
            }
            if (!is_null($domain->parser_class)) {
                $parserClass = app()->make('Invigor\Crawler\Repositories\Parsers\\' . $domain->parser_class);
            }
            $currencyFormatterClass = null;
            if (!is_null($domain->currency_formatter_class)) {
                $currencyFormatterClass = app()->make('Invigor\Crawler\Repositories\CurrencyFormatters\\' . $domain->currency_formatter_class);
            }

            if (!is_null($domain)) {
                $options = array(
                    "url" => $this->request->get('site_url'),
                );
                $content = $this->crawlerRepo->crawlPage($options, $crawlerClass);
                if (!is_null($content) && strlen($content) != 0) {
                    for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
                        $xpath = $domain->preference->toArray()["xpath_{$xpathIndex}"];
                        if ($xpath != null || (!is_null($domain->crawler_class) || !is_null($domain->parser_class))) {
                            $result = $this->crawlerRepo->parserPrice($xpath, $content, $parserClass, $currencyFormatterClass);
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

        $sites = Site::where("site_url", $this->request->get('site_url'))->whereNotNull("recent_price")->get();
//            $sites = $this->siteManager->getSiteByColumn('site_url', $this->request->get('site_url'));
        $sitePrices = array();
        foreach ($sites as $key => $site) {
            if (!in_array($site->recent_price, $sitePrices) && (!isset($targetDomain) || $targetDomain['recent_price'] != $site->recent_price)) {
                $sitePrices[] = $site->recent_price;
            } else {
                unset($sites[$key]);
            }
        }
        $siteURL = $this->request->get('site_url');
        $status = true;
        event(new SitePricesViewed());
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'sites', 'targetDomain', 'siteURL']));
            } else {
                return view('products.site.choose_price')->with(compact(['status', 'sites', 'targetDomain', 'siteURL']));
            }
        } else {
            //TODO implement if needed
        }
    }

    public function setMyPrice($site_id)
    {
        /*TODO validate my price from request*/

        $site = $this->siteRepo->getSite($site_id);
        $myPrice = $this->request->get("my_price");
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
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status', 'site']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function updateOrder()
    {
        /*TODO validation here*/
        $status = false;
        if ($this->request->has('order')) {
            $order = $this->request->get('order');
            foreach ($order as $key => $ord) {
                $site = $this->siteRepo->getSite($ord['site_id']);
                if (!is_null($site) && intval($ord['site_order']) != 0) {
                    $site->site_order = intval($ord['site_order']);
                    $site->save();
                }
            }
            $status = true;
        }

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $site = $this->siteRepo->getSite($id);
        event(new SiteDeleting($site));
        $this->siteRepo->deleteSite($id);
        event(new SiteDeleted($site));
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }
}
