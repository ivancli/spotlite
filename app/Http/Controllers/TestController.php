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
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        Excel::create('test', function ($excel) {
            $excel->sheet('sheet 1', function ($sheet) {
                $user = User::findOrFail(209);

                $data = [];
                foreach ($user->categories as $category) {
                    foreach ($category->products as $product) {
                        foreach ($product->sites as $site) {
                            $data [] = [
                                'category' => $category->category_name,
                                'product' => $product->product_name,
                                'url' => $site->site_url,
                                'price' => $site->recent_price
                            ];
                        }
                    }
                }
                $sheet->fromArray($data);


            });
        })->download('xls');
    }
}