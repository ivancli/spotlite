<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 11:49 AM
 */

namespace App\Http\Controllers\Crawler;


use App\Contracts\ProductManagement\ProductSiteManager;
use App\Contracts\ProductManagement\SiteManager;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

class SiteController extends Controller
{
    protected $productSiteManager;
    protected $siteManager;
    protected $queryFilter;

    public function __construct(ProductSiteManager $productSiteManager, SiteManager $siteManager, QueryFilter $queryFilter)
    {
        $this->productSiteManager = $productSiteManager;
        $this->siteManager = $siteManager;
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

        $xpath = $site->site_xpath;
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
                $errors = "Price is incorrect";
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
    }

    public function update(Request $request, $site_id)
    {
        $site = $this->siteManager->updateSite($site_id, $request->all());
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