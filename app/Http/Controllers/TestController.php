<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use App\Contracts\Repository\Ebay\EbayContract;
use App\Jobs\CrawlSite;
use App\Models\Crawler;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    var $request;
    var $ebayRepo;

    public function __construct(Request $request, EbayContract $ebayContract)
    {
        $this->request = $request;
        $this->ebayRepo = $ebayContract;
    }

    public function index()
    {
        $url = "http://www.ebay.com.au/itm/Tornado-Bluetooth-Smart-Padlock-/222389229596?&_trksid=p2056016.m2518.l4276";
        $path = parse_url($url)['path'];
        $tokens = explode('/', $path);
        $itemId = $tokens[count($tokens) - 1];
        if ($itemId) {
            $item = $this->ebayRepo->getItem($itemId);
            if (isset($item->errors)) {
                $itemGroup = $this->ebayRepo->getItemGroup($itemId);
                if (isset($itemGroup->items) && is_array($itemGroup->items)) {
                    $itemGroupItem = array_first($itemGroup->items);
                    $itemId = $itemGroupItem->itemId;
                    $item = $this->ebayRepo->getItem($itemId);
                }
            }
            $ebayItem = $site->ebayItem;
            if (is_null($ebayItem)) {
                $ebayItem = $site->ebayItem()->save(new EbayItem());
            }

            $ebayItem->title = isset($item->title) ? $item->title : null;
            $ebayItem->subtitle = isset($item->subtitle) ? $item->subtitle : null;
            $ebayItem->shortDescription = isset($item->shortDescription) ? $item->shortDescription : null;
            $ebayItem->price = isset($item->price) && isset($item->price->value) ? $item->price->value : null;
            $ebayItem->currency = isset($item->price) && isset($item->price->currency) ? $item->price->currency : null;
            $ebayItem->category = isset($item->categoryPath) ? $item->categoryPath : null;
            $ebayItem->condition = isset($item->condition) ? $item->condition : null;
            $ebayItem->location_city = isset($item->itemLocation) && isset($item->itemLocation->city) ? $item->itemLocation->city : null;
            $ebayItem->location_postcode = isset($item->itemLocation) && isset($item->itemLocation->postalCode) ? $item->itemLocation->postalCode : null;
            $ebayItem->location_country = isset($item->itemLocation) && isset($item->itemLocation->country) ? $item->itemLocation->country : null;
            $ebayItem->image_url = isset($item->image) && isset($item->image->imageUrl) ? $item->image->imageUrl : null;
            $ebayItem->brand = isset($item->brand) ? $item->brand : null;
            $ebayItem->seller_username = isset($item->seller) && isset($item->seller->username) ? $item->seller->username : null;

            $ebayItem->save();
        }
//        $site = Site::findOrFail(45835);
//        $product = $site->product;
//        if (!is_null($product)) {
//            $companyUrl = $product->user->company_url;
//            $ebayUsername = $product->user->ebay_username;
//
//            $myCompanyDomain = parse_url($companyUrl)['host'];
//
//            list($dummy, $subdomainSplitted) = explode('.', $site->domain, 2);
//            list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);
//
//            //matching both sub-domain and domain
//            $ebayItem = $site->ebayItem;
//            if (!is_null($ebayUsername) && !empty($ebayUsername) && !is_null($ebayItem)) {
//                return $ebayUsername == $ebayItem->seller_username ? 'y' : 'n';
//            } elseif ($subdomainSplitted == $domainSplitted) {
//                return 'y';
//            }
//        }
//        return 'n';

    }
}