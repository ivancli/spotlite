<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/15/2016
 * Time: 1:47 PM
 */

namespace App\Http\Controllers\Crawler;


use App\Contracts\CrawlerManagement\CrawlerManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    protected $crawlerManager;

    public function __construct(CrawlerManager $crawlerManager)
    {
        $this->crawlerManager = $crawlerManager;
    }

    public function edit($crawler_id)
    {
        $crawler = $this->crawlerManager->getCrawler($crawler_id);
        return view('admin.site.forms.crawler')->with(compact(['crawler']));
    }

    public function update(Request $request, $crawler_id)
    {
        /*TODO validation here*/
        $input = $request->all();
        if($request->has('crawler_class') && strlen($input['crawler_class']) == 0){
            $input['crawler_class'] = null;
        }

        if($request->has('parser_class') && strlen($input['parser_class']) == 0){
            $input['parser_class'] = null;
        }

        $crawler = $this->crawlerManager->getCrawler($crawler_id);
        $crawler = $this->crawlerManager->updateCrawler($crawler_id, $request->all());
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