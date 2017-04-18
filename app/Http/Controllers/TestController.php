<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use App\Contracts\Repository\Ebay\EbayContract;
use App\Models\EbayItem;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $site = Site::findOrFail(9565);
        if (strpos($site->domain, 'www.ebay.com') !== false) {
            $url = $site->site_url;
            $url = 'http://www.ebay.com.au/itm/1-x-2017-Australian-1-one-dollar-100-Years-Anzac-Coin-from-Mint-Bag-RARE-UNC-/182529585621?hash=item2a7f9c71d5';
            $path = parse_url($url)['path'];
            $tokens = explode('/', $path);
            $itemId = $tokens[count($tokens) - 1];
            if ($itemId) {
                $item = $this->ebayRepo->getItem($itemId);
                dd($item);
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
        }

    }
}