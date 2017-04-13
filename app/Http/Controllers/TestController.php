<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/3/2017
 * Time: 10:50 AM
 */

namespace App\Http\Controllers;

use App\Contracts\Repository\Ebay\EbayContract;
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
        $site = Site::findOrFail(26);
        if (strpos($site->domain, 'www.ebay.com') !== false) {
            $url = $site->site_url;
            $path = parse_url($url)['path'];
            $tokens = explode('/', $path);
            $itemId = $tokens[count($tokens) - 1];
            if ($itemId) {
                $this->ebayRepo->getItem($itemId);
            }
        }
    }
}