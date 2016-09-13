<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 11:49 AM
 */

namespace App\Http\Controllers\Crawler;


use App\Contracts\ProductManagement\DomainManager;
use App\Contracts\ProductManagement\ProductSiteManager;
use App\Contracts\ProductManagement\SiteManager;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

class SiteController extends Controller
{
    protected $productSiteManager;
    protected $siteManager;
    protected $queryFilter;
    protected $domainManager;

    public function __construct(ProductSiteManager $productSiteManager, SiteManager $siteManager, DomainManager $domainManager, QueryFilter $queryFilter)
    {
        $this->productSiteManager = $productSiteManager;
        $this->siteManager = $siteManager;
        $this->domainManager = $domainManager;
        $this->queryFilter = $queryFilter;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sites = $this->siteManager->getDataTablesSites($this->queryFilter);
            if ($request->wantsJson()) {
                return response()->json($sites);
            } else {
                return $sites;
            }
        } else {
            return view('admin.site.index');
        }
    }

    public function sendTest(Request $request, CrawlerInterface $crawler, ParserInterface $parser, $site_id)
    {
        $site = $this->siteManager->getSite($site_id);

        $options = array(
            "url" => $site->site_url,
        );
        $crawler->setOptions($options);
        $crawler->loadHTML();
        $html = $crawler->getHTML();

        if (is_null($html) || strlen($html) == 0) {
            $status = false;
            $errors = array("HTML is blank");
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                /*TODO implement if needed*/
            }
        }

        $xpath = $site->site_xpath;
        if (is_null($xpath)) {
            $domain_url = parse_url($site->site_url)['host'];
            $domain = Domain::where('domain_url', $domain_url)->first();
            if (!is_null($domain)) {
                $xpath = $domain->domain_xpath;
            }
        }


        $options = array(
            "xpath" => $xpath,
        );
        $parser->setOptions($options);
        $parser->setHTML($html);
        $parser->init();
        $result = $parser->parseHTML();
        if (!is_null($result) && is_string($result)) {
            $price = str_replace('$', '', $result);
            $price = floatval($price);
            if ($price > 0) {
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['status', 'price']));
                    } else {
                        return compact(['status', 'price']);
                    }
                } else {
                    /*TODO implement if needed*/
                }
            } else {
                $status = false;
                $errors = array("The crawled price is incorrect");
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['status', 'errors']));
                    } else {
                        return compact(['status', 'errors']);
                    }
                } else {
                    /*TODO implement if needed*/
                }
            }
        } else {
            $status = false;
            $errors = array("xPath is incorrect, or the site might be loaded through ajax.");
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                /*TODO implement if needed*/
            }
        }
    }

    public function update(Request $request, $site_id)
    {
        $input = $request->all();
        if (isset($input['site_xpath']) && strlen($input['site_xpath']) == 0) {
            $input['site_xpath'] = null;
        }
        $site = $this->siteManager->updateSite($site_id, $input);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status', 'site']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }
}