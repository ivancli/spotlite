<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/15/2016
 * Time: 1:47 PM
 */

namespace App\Http\Controllers\Crawler;


use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    protected $crawler;

    public function __construct(CrawlerContract $crawler)
    {
        $this->middleware('permission:update_admin_site_preference', ['only' => ['edit', 'update']]);


        $this->crawler = $crawler;
    }

    public function edit($crawler_id)
    {
        $crawler = $this->crawler->getCrawler($crawler_id);
        return view('admin.site.forms.crawler')->with(compact(['crawler']));
    }

    public function update(Request $request, $crawler_id)
    {
        /*TODO validation here*/
        $input = $request->all();
        if (!$request->has('crawler_class') || strlen($input['crawler_class']) == 0) {
            $input['crawler_class'] = null;
        }

        if (!$request->has('parser_class') || strlen($input['parser_class']) == 0) {
            $input['parser_class'] = null;
        }

        if (!$request->has('currency_formatter_class') || strlen($input['currency_formatter_class']) == 0) {
            $input['currency_formatter_class'] = null;
        }

        $crawler = $this->crawler->getCrawler($crawler_id);
        $crawler = $this->crawler->updateCrawler($crawler_id, $input);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'crawler']));
            } else {
                return compact(['status', 'crawler']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }
}