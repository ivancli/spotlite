<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 11:49 AM
 */

namespace App\Http\Controllers\Crawler;


use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Contracts\Repository\Product\Domain\DomainContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Exceptions\ValidationException;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunctions;
use App\Models\Domain;
use App\Validators\Crawler\Site\StoreValidator;
use App\Validators\Crawler\Site\UpdateValidator;
use Illuminate\Http\Request;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

class SiteController extends Controller
{
    use CommonFunctions;

    protected $siteRepo;
    protected $domainRepo;
    protected $crawlerRepo;

    protected $queryFilter;

    protected $storeValidator;
    protected $updateValidator;

    public function __construct(SiteContract $siteContract, DomainContract $domainContract, CrawlerContract $crawlerContract,
                                QueryFilter $queryFilter,
                                StoreValidator $storeValidator, UpdateValidator $updateValidator)
    {
        $this->siteRepo = $siteContract;
        $this->domainRepo = $domainContract;
        $this->crawlerRepo = $crawlerContract;

        $this->queryFilter = $queryFilter;

        $this->storeValidator = $storeValidator;
        $this->updateValidator = $updateValidator;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sites = $this->siteRepo->getDataTablesSites($this->queryFilter);
            if ($request->wantsJson()) {
                return response()->json($sites);
            } else {
                return $sites;
            }
        } else {
            return view('admin.site.index');
        }
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.site.forms.add_site');
        } else {
            /*TODO implement this if needed*/
        }
    }

    public function store(Request $request)
    {
        try {
            $this->storeValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        $site = $this->siteRepo->createSite($request->all());
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'site']));
            } else {
                return compact(['status', 'site']);
            }
        } else {
            return redirect()->route('admin.site.index');
        }
    }

    public function sendTest(Request $request, CrawlerInterface $crawlerClass, ParserInterface $parserClass, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);
        if (!is_null($site->crawler->crawler_class)) {
            $crawlerClass = app()->make('Invigor\Crawler\Repositories\Crawlers\\' . $site->crawler->crawler_class);
        }
        if (!is_null($site->crawler->parser_class)) {
            $parserClass = app()->make('Invigor\Crawler\Repositories\Parsers\\' . $site->crawler->parser_class);
        }
        $options = array(
            "url" => $site->site_url,
        );
        $content = $this->crawlerRepo->crawlPage($options, $crawlerClass);

        if (is_null($content) || strlen($content) == 0) {
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

        for ($xpathIndex = 1; $xpathIndex < 6; $xpathIndex++) {
            $xpath = $site->preference->toArray()["xpath_{$xpathIndex}"];

            if ($xpath != null || (!is_null($site->crawler->crawler_class) || !is_null($site->crawler->parser_class))) {
                $result = $this->crawlerRepo->parserPrice($xpath, $content, $parserClass);

                if (isset($result['status']) && $result['status'] == true) {
                    $price = $result['price'];
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
                    if (isset($result['error'])) {
                        if ($result['error'] == "incorrect price") {
                            $errors = array("The crawled price is incorrect");
                            continue;
                        } elseif ($result['error'] == "incorrect xpath") {
                            $errors = array("xPath is incorrect, or the site might be loaded through ajax.");
                            continue;
                        }
                    }
                }
            } else {
                if ($xpathIndex == 1) {
                    $status = false;
                    $errors = array("xPath not specified.");
                }
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['status', 'errors']));
                    } else {
                        return compact(['status', 'errors']);
                    }
                }
            }
        }
    }

    /**
     * @param Request $request
     * @param $site_id
     * @return SiteController|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editxPath(Request $request, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);
        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                return view('admin.site.forms.xpath')->with(compact(['site']));
            }
        } else {
            return view('admin.site.forms.xpath')->with(compact(['site']));
        }
    }

    public function updatexPath(Request $request, $site_id)
    {
        $input = array_map(function ($e) {
            return $e ?: null;
        }, $request->all());

        $site = $this->siteRepo->getSite($site_id);
        $site->preference->update($input);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /* TODO implement this if necessary */
        }
    }

    /**
     * At the moment, update function is used in updating xpath, no site_url required.
     *
     * @param Request $request
     * @param $site_id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $site_id)
    {
        $input = $request->all();
        $site = $this->siteRepo->getSite($site_id);
        $preference = $site->preference;
        if (isset($input['site_xpath']) && strlen($input['site_xpath']) == 0) {
            $preference->xpath_1 = null;
            $preference->save();
        } else {
            $preference->xpath_1 = $input['site_xpath'];
            $preference->save();
        }
//        $site = $this->siteRepo->updateSite($site_id, $input);
        if ($site->status == 'null_xpath') {
            $site->statusWaiting();
        }
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

    public function destroy(Request $request, $site_id)
    {
        $site = $this->siteRepo->getSite($site_id);
        $site->delete();
        $status = true;


        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('admin.site.index');
        }
    }
}