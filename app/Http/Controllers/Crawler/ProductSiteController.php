<?php
namespace App\Http\Controllers\Crawler;

use App\Contracts\ProductManagement\ProductSiteManager;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Invigor\Crawler\Contracts\CrawlerInterface;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/12/2016
 * Time: 5:29 PM
 */
class ProductSiteController extends Controller
{
    protected $productSiteManager;
    protected $queryFilter;

    public function __construct(ProductSiteManager $productSiteManager, QueryFilter $queryFilter)
    {
        $this->productSiteManager = $productSiteManager;
        $this->queryFilter = $queryFilter;
//        app()->bind('Invigor\Crawler\Contracts\CrawlerInterface', 'Invigor\Crawler\Repositories\DefaultCrawler');
    }

//    public function index(CrawlerInterface $crawler)
    public function index(Request $request)
    {
//        $options = array(
//            "url" => "https://www.bigw.com.au/product/smart-value-mega-towel/p/WCC100000000300062/"
//        );
//
//        $crawler->setOptions($options);
//        $crawler->loadHTML();
//        return ($crawler->getHTML());
        if ($request->ajax()) {
            $productSites = $this->productSiteManager->getDataTablesProductSites($this->queryFilter);
            if ($request->wantsJson()) {
                return response()->json($productSites);
            } else {
                return $productSites;
            }
        } else {
            return view('admin.product_site.index');
        }
    }
}