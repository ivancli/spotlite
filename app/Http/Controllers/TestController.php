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
use App\Models\Category;
use App\Models\Crawler;
use App\Models\Site;
use App\Models\SitePreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Invigor\Crawler\Repositories\Crawlers;
use Invigor\Crawler\Repositories\Crawlers\DefaultCrawler;
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
        $user_id = $this->request->get('user_id');
        $categories = Category::where('user_id', '=', $user_id)->get();
        $data = $categories;
        $fileName = "full_report";
        $excel = Excel::create("{$user_id}_{$fileName}", function ($excel) use ($categories, $data, $fileName) {
            $excel->sheet("full_report", function ($sheet) use ($data) {
                $sheet->loadview('products.report.excel.user', compact(['data']));
                $sheet->setColumnFormat(array(
                    'D' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2,
                ));
                $sheet->setWidth('A', 30);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 30);
                $sheet->setWidth('D', 20);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 20);
                $sheet->setWidth('G', 20);
            });
        })->store('csv');
    }
}