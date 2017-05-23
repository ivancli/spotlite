<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/28/2017
 * Time: 9:20 AM
 */

namespace App\Http\Controllers\Product;


use App\Events\Products\Positioning\AfterShow;
use App\Events\Products\Positioning\AfterIndex;
use App\Events\Products\Positioning\BeforeIndex;
use App\Events\Products\Positioning\BeforeShow;
use App\Http\Controllers\Controller;
use App\Validators\Product\Positioning\ShowValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDO;

class PositioningController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    public function index()
    {
        event(new BeforeIndex());
        $user = auth()->user();
        $domains = [];
        $results = DB::table('sites')
            ->join('products', function ($query) {
                $query->on('sites.product_id', '=', 'products.product_id');
            })->where('products.user_id', '=', $user->getKey())
            ->select(DB::raw('DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(site_url, \'/\', 3), \'://\', -1), \'/\', 1), \'?\', 1) AS domain'))->get();

        foreach ($results as $result) {
            $userDomain = $user->domains()->where('domain', '=', $result->domain)->get();
            if ($userDomain->count() > 0) {
                $domains [$result->domain] = $userDomain->first()->name;
            } else {
                $domains[$result->domain] = $result->domain;
            }
        }
        $domains = array_sort($domains, function ($value) {
            return $value;
        });

        $ebaySellerUsernames = [];
        $ebayResults = DB::table('ebay_items')->join('sites', 'ebay_items.site_id', '=', 'sites.site_id')->join('products', 'sites.product_id', '=', 'products.product_id')
            ->where('user_id', '=', auth()->user()->getKey())->whereNotNull('seller_username')
            ->select('seller_username')->distinct()
            ->get();
        $ebaySellerUsernames = array_map(function ($item) {
            return $item->seller_username;
        }, $ebayResults);

        $categories = $user->categories;
        $productMetas = $user->productMetas;
        $brands = $productMetas->sortBy('brand')->pluck('brand')->unique();
        $suppliers = $productMetas->sortBy('supplier')->pluck('supplier')->unique();

        event(new AfterIndex());

        return view('products.positioning.index')->with(compact(['domains', 'ebaySellerUsernames', 'categories', 'brands', 'suppliers']));
    }

    public function show(ShowValidator $showValidator)
    {
        event(new BeforeShow());
        DB::enableQueryLog();

        $showValidator->validate($this->request->all());
        $user = auth()->user();
        $productBuilder = $user->products();
        $productBuilder = DB::table('products AS products')->where('products.user_id', '=', $user->getKey());

        $select = [
            'cheapestSite.site_urls as cheapest_site_url',
            'cheapestSite.recent_price as cheapest_recent_price',
            'expensiveSite.site_urls as expensive_site_url',
            'expensiveSite.recent_price as expensive_recent_price',
            'secondCheapestSite.site_urls as second_cheapest_site_url',
            'secondCheapestSite.recent_price as second_cheapest_recent_price',
            'products.*',
            'categories.*',
            DB::raw('COUNT(sites.site_id) as number_of_sites')
        ];

        $excludeQuery = "";
        $excludeQuery = " WHERE ";
        if ($this->request->has('exclude') && is_array($this->request->get('exclude'))) {
            foreach ($this->request->get('exclude') as $index => $exclude) {
                if ($index != 0) {
                    $excludeQuery .= " AND ";
                }
                if (strpos($exclude, 'eBay: ') !== false) {
                    $excludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                } else {
                    $excludeQuery .= " a.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                }
            }
            $excludeQuery .= " AND a.status != 'invalid'";
        } else {
            $excludeQuery .= " a.status != 'invalid'";
        }

        $subExcludeQuery = "";
        $subExcludeQuery = " WHERE ";
        $secondCheapestSubExcludeQuery = " AND ";
        if ($this->request->has('exclude') && is_array($this->request->get('exclude'))) {
            foreach ($this->request->get('exclude') as $index => $exclude) {
                if ($index != 0) {
                    $subExcludeQuery .= " AND ";
                    $secondCheapestSubExcludeQuery .= " AND ";
                }
                if (strpos($exclude, 'eBay: ') !== false) {
                    $subExcludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                    $secondCheapestSubExcludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                } else {
                    $subExcludeQuery .= " sites.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                    $secondCheapestSubExcludeQuery .= " sites.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                }
            }
            $subExcludeQuery .= " AND sites.status != 'invalid'";
            $secondCheapestSubExcludeQuery .= " AND sites.status != 'invalid'";
        } else {
            $subExcludeQuery .= " sites.status != 'invalid'";
            $secondCheapestSubExcludeQuery .= " sites.status != 'invalid'";
        }


        $cheapestSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT product_id, MIN(recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) ' . $subExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS cheapestSite');
        $expensiveSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT product_id, MAX(recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) ' . $subExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS expensiveSite');
        $nextCheapestSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT sites.product_id, MIN(sites.recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) LEFT JOIN (	SELECT product_id, MIN(recent_price) recent_price FROM sites GROUP BY product_id) as a ON (sites.product_id=a.product_id) WHERE sites.recent_price != a.recent_price ' . $secondCheapestSubExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS secondCheapestSite');

        $productBuilder->leftJoin($cheapestSiteQuery, function ($join) {
            $join->on('cheapestSite.product_id', '=', 'products.product_id');
        });
        $productBuilder->leftJoin($expensiveSiteQuery, function ($join) {
            $join->on('expensiveSite.product_id', '=', 'products.product_id');
        });
        $productBuilder->leftJoin($nextCheapestSiteQuery, function ($join) {
            $join->on('secondCheapestSite.product_id', '=', 'products.product_id');
        });


        if ($this->request->has('reference')) {
            $referenceDomain = $this->request->get('reference');
            if (strpos($referenceDomain, 'eBay: ') !== false) {
                $ebayUsername = str_replace('eBay: ', '', $referenceDomain);
                $referenceQuery = DB::raw('(SELECT sites.*, ebay_items.seller_username FROM ebay_items JOIN sites USING(site_id) WHERE seller_username LIKE "%' . addslashes(urlencode($ebayUsername)) . '%") AS reference');
                $productBuilder->leftJoin($referenceQuery, function ($join) {
                    $join->on('reference.product_id', '=', 'products.product_id');
                });
            } else {
                $referenceQuery = DB::raw('(SELECT * FROM sites WHERE site_url LIKE "%' . addslashes(urlencode($referenceDomain)) . '%") AS reference');

                $productBuilder->leftJoin($referenceQuery, function ($join) {
                    $join->on('reference.product_id', '=', 'products.product_id');
                });
            }
            $select[] = 'reference.site_url as reference_site_url';
            $select[] = 'reference.recent_price as reference_recent_price';
            $select[] = DB::raw('ABS(reference.recent_price - cheapestSite.recent_price) as diff_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)/reference.recent_price as percent_diff_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - expensiveSite.recent_price) as diff_expensive');
            $select[] = DB::raw('ABS(reference.recent_price - expensiveSite.recent_price)/reference.recent_price as percent_diff_expensive');
            $select[] = DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price) as diff_second_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)/reference.recent_price as percent_diff_second_cheapest');
        }

        if ($this->request->has('category')) {
            $category = $this->request->get('category');
            $productBuilder->join('categories', function ($join) use ($category) {
                $join->on('products.category_id', '=', 'categories.category_id')->where('categories.category_id', '=', $category);
            });
        } else {
            $productBuilder->join('categories', 'categories.category_id', '=', 'products.category_id');
        }

        if ($this->request->has('brand') || $this->request->has('supplier')) {
            $brand = $this->request->get('brand');
            $supplier = $this->request->get('supplier');
            $productBuilder->join('product_metas', function ($join) use ($brand, $supplier) {
                $join->on('products.product_id', '=', 'product_metas.product_id');
                if (!is_null($brand) && !empty($brand)) {
                    $join->where('product_metas.brand', '=', $brand);
                }
                if (!is_null($supplier) && !empty($supplier)) {
                    $join->where('product_metas.supplier', '=', $supplier);
                }
            });
        }

        if ($this->request->has('search') && is_array($this->request->get('search')) && !empty(array_get($this->request->get('search'), 'value'))) {
            $keyword = array_get($this->request->get('search'), 'value');
            $productBuilder->where(function ($query) use ($keyword) {
                $query->where('product_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('category_name', 'LIKE', "%{$keyword}%");
                if ($this->request->has('reference')) {
                    $query->orWhere('reference.recent_price', 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)/reference.recent_price'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)/reference.recent_price'), 'LIKE', "%{$keyword}%");
                }
                $query->orWhere('cheapestSite.site_urls', 'LIKE', "%{$keyword}%")
                    ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%")
                    ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%");
            });
        }

        if ($this->request->has('position') && !empty($this->request->get('position'))) {
            switch ($this->request->get('position')) {
                case "not_cheapest":
                    $productBuilder->where(function ($query) {
                        $query->where(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), '!=', 0)
                            ->orWhereNull(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'));
                    });
                    break;
                case "most_expensive":
                    $productBuilder->where(DB::raw('ABS(expensiveSite.recent_price - reference.recent_price)'), '==', 0);
                    break;
                case "cheapest":
                    $productBuilder->where(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), '=', 0);
                    break;
                default:
            }
        }


        $productBuilder->select($select);
        $recordTotal = $user->products()->count();
        $recordsFiltered = $productBuilder->count();

        $productBuilder->leftJoin('sites', 'products.product_id', '=', 'sites.product_id');
        $productBuilder->groupBy('products.product_id');
        $productBuilder->having('number_of_sites', '>', 1);

        if ($this->request->has('order')) {
            $order = array_first($this->request->get('order'));
            $orderColumn = array_get($order, 'column');
            $orderSequence = array_get($order, 'dir');
            if ($orderColumn) {
                if ($orderColumn == 'diff_ref_cheapest' && $this->request->has('reference')) {
                    if ($this->request->has('reference')) {
                        if ($this->request->has('position') && $this->request->get('position') == 'cheapest') {
                            $productBuilder = $productBuilder->orderBy('diff_second_cheapest', $orderSequence);
                        } else {
                            $productBuilder = $productBuilder->orderBy('diff_cheapest', $orderSequence);
                        }
                    } else {
                        $productBuilder = $productBuilder->orderBy('categories.category_name', $orderSequence);
                    }
                } elseif ($orderColumn == 'percent_diff_ref_cheapest') {
                    if ($this->request->has('reference')) {
                        if ($this->request->has('position') && $this->request->get('position') == 'cheapest') {
                            $productBuilder = $productBuilder->orderBy('percent_diff_second_cheapest', $orderSequence);
                        } else {
                            $productBuilder = $productBuilder->orderBy('percent_diff_cheapest', $orderSequence);
                        }
                    } else {
                        $productBuilder = $productBuilder->orderBy('categories.category_name', $orderSequence);
                    }
                } else {
                    $productBuilder = $productBuilder->orderBy($orderColumn, $orderSequence);
                }
            }
        }

        if ($this->request->has('start')) {
            $productBuilder = $productBuilder->skip($this->request->get('start'));
        }
        if ($this->request->has('length')) {
            $productBuilder = $productBuilder->take($this->request->get('length'));
        }
        $products = $productBuilder->get();

        $draw = $this->request->get('draw');
        $data = $products;

        event(new AfterShow());

        return compact(['data', 'draw', 'recordTotal', 'recordsFiltered']);
    }

    public function export()
    {
        $data = $this->request->get('data');
        $data = json_decode($data, true);
        $this->request->merge($data);

        $user = auth()->user();
        $productBuilder = $user->products();
        $productBuilder = DB::table('products AS products')->where('products.user_id', '=', $user->getKey());

        $productBuilder->leftJoin('product_metas', 'products.product_id', '=', 'product_metas.product_id');

        $select = [
            'cheapestSite.site_urls as cheapest_site_url',
            'cheapestSite.recent_price as cheapest_recent_price',
            'expensiveSite.site_urls as expensive_site_url',
            'expensiveSite.recent_price as expensive_recent_price',
            'secondCheapestSite.site_urls as second_cheapest_site_url',
            'secondCheapestSite.recent_price as second_cheapest_recent_price',
            'products.*',
            'product_metas.*',
            'categories.*',
            DB::raw('COUNT(sites.site_id) as number_of_sites')
        ];

        $excludeQuery = "";
        $excludeQuery = " WHERE ";
        if ($this->request->has('exclude') && is_array($this->request->get('exclude'))) {
            foreach ($this->request->get('exclude') as $index => $exclude) {
                if ($index != 0) {
                    $excludeQuery .= " AND ";
                }
                if (strpos($exclude, 'eBay: ') !== false) {
                    $excludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                } else {
                    $excludeQuery .= " a.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                }
            }
            $excludeQuery .= " AND a.status != 'invalid'";
        } else {
            $excludeQuery .= " a.status != 'invalid'";
        }

        $subExcludeQuery = "";
        $subExcludeQuery = " WHERE ";
        $secondCheapestSubExcludeQuery = " AND ";
        if ($this->request->has('exclude') && is_array($this->request->get('exclude'))) {
            foreach ($this->request->get('exclude') as $index => $exclude) {
                if ($index != 0) {
                    $subExcludeQuery .= " AND ";
                    $secondCheapestSubExcludeQuery .= " AND ";
                }
                if (strpos($exclude, 'eBay: ') !== false) {
                    $subExcludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                    $secondCheapestSubExcludeQuery .= " ebay_items.seller_username != '" . addslashes(urlencode(str_replace('eBay: ', '', $exclude))) . "' ";
                } else {
                    $subExcludeQuery .= " sites.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                    $secondCheapestSubExcludeQuery .= " sites.site_url NOT LIKE '%" . addslashes(urlencode($exclude)) . "%' ";
                }
            }
            $subExcludeQuery .= " AND sites.status != 'invalid'";
            $secondCheapestSubExcludeQuery .= " AND sites.status != 'invalid'";
        } else {
            $subExcludeQuery .= " sites.status != 'invalid'";
            $secondCheapestSubExcludeQuery .= " sites.status != 'invalid'";
        }


        $cheapestSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT product_id, MIN(recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) ' . $subExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS cheapestSite');
        $expensiveSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT product_id, MAX(recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) ' . $subExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS expensiveSite');
        $nextCheapestSiteQuery = DB::raw('(SELECT b.*, GROUP_CONCAT(CONCAT(a.site_url, \'$#$\', IFNULL(CONCAT(\'eBay: \', ebay_items.seller_username), \'\')) SEPARATOR \'$ $\') site_urls FROM (SELECT sites.product_id, MIN(sites.recent_price) recent_price FROM sites LEFT JOIN ebay_items ON(sites.site_id=ebay_items.site_id) LEFT JOIN (	SELECT product_id, MIN(recent_price) recent_price FROM sites GROUP BY product_id) as a ON (sites.product_id=a.product_id) WHERE sites.recent_price != a.recent_price ' . $secondCheapestSubExcludeQuery . ' GROUP BY product_id) b LEFT JOIN sites a ON(a.recent_price=b.recent_price AND a.product_id=b.product_id) LEFT JOIN ebay_items ON(a.site_id=ebay_items.site_id) ' . $excludeQuery . ' GROUP BY product_id) AS secondCheapestSite');

        $productBuilder->leftJoin($cheapestSiteQuery, function ($join) {
            $join->on('cheapestSite.product_id', '=', 'products.product_id');
        });
        $productBuilder->leftJoin($expensiveSiteQuery, function ($join) {
            $join->on('expensiveSite.product_id', '=', 'products.product_id');
        });
        $productBuilder->leftJoin($nextCheapestSiteQuery, function ($join) {
            $join->on('secondCheapestSite.product_id', '=', 'products.product_id');
        });


        if ($this->request->has('reference')) {
            $referenceDomain = $this->request->get('reference');
            if (strpos($referenceDomain, 'eBay: ') !== false) {
                $ebayUsername = str_replace('eBay: ', '', $referenceDomain);
                $referenceQuery = DB::raw('(SELECT sites.*, ebay_items.seller_username FROM ebay_items JOIN sites USING(site_id) WHERE seller_username LIKE "%' . addslashes(urlencode($ebayUsername)) . '%") AS reference');
                $productBuilder->leftJoin($referenceQuery, function ($join) {
                    $join->on('reference.product_id', '=', 'products.product_id');
                });
            } else {
                $referenceQuery = DB::raw('(SELECT * FROM sites WHERE site_url LIKE "%' . addslashes(urlencode($referenceDomain)) . '%") AS reference');

                $productBuilder->leftJoin($referenceQuery, function ($join) {
                    $join->on('reference.product_id', '=', 'products.product_id');
                });
            }
            $select[] = 'reference.site_url as reference_site_url';
            $select[] = 'reference.recent_price as reference_recent_price';
            $select[] = DB::raw('ABS(reference.recent_price - cheapestSite.recent_price) as diff_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)/reference.recent_price as percent_diff_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - expensiveSite.recent_price) as diff_expensive');
            $select[] = DB::raw('ABS(reference.recent_price - expensiveSite.recent_price)/reference.recent_price as percent_diff_expensive');
            $select[] = DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price) as diff_second_cheapest');
            $select[] = DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)/reference.recent_price as percent_diff_second_cheapest');
        }

        if ($this->request->has('category')) {
            $category = $this->request->get('category');
            $productBuilder->join('categories', function ($join) use ($category) {
                $join->on('products.category_id', '=', 'categories.category_id')->where('categories.category_id', '=', $category);
            });
        } else {
            $productBuilder->join('categories', 'categories.category_id', '=', 'products.category_id');
        }

        if ($this->request->has('brand') || $this->request->has('supplier')) {
            $brand = $this->request->get('brand');
            $supplier = $this->request->get('supplier');
            $productBuilder->join('product_metas', function ($join) use ($brand, $supplier) {
                $join->on('products.product_id', '=', 'product_metas.product_id');
                if (!is_null($brand) && !empty($brand)) {
                    $join->where('product_metas.brand', '=', $brand);
                }
                if (!is_null($supplier) && !empty($supplier)) {
                    $join->where('product_metas.supplier', '=', $supplier);
                }
            });
        }

        if ($this->request->has('search') && is_array($this->request->get('search')) && !empty(array_get($this->request->get('search'), 'value'))) {
            $keyword = array_get($this->request->get('search'), 'value');
            $productBuilder->where(function ($query) use ($keyword) {
                $query->where('product_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('category_name', 'LIKE', "%{$keyword}%");
                if ($this->request->has('reference')) {
                    $query->orWhere('reference.recent_price', 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)/reference.recent_price'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)'), 'LIKE', "%{$keyword}%");
                    $query->orWhere(DB::raw('ABS(reference.recent_price - secondCheapestSite.recent_price)/reference.recent_price'), 'LIKE', "%{$keyword}%");
                }
                $query->orWhere('cheapestSite.site_urls', 'LIKE', "%{$keyword}%")
                    ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%")
                    ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%");
            });

        }

        if ($this->request->has('position') && !empty($this->request->get('position'))) {
            switch ($this->request->get('position')) {
                case "not_cheapest":
                    $productBuilder->where(function ($query) {
                        $query->where(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), '!=', 0)
                            ->orWhereNull(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'));
                    });
                    break;
                case "most_expensive":
                    $productBuilder->where(DB::raw('ABS(expensiveSite.recent_price - reference.recent_price)'), '==', 0);
                    break;
                case "cheapest":
                    $productBuilder->where(DB::raw('ABS(reference.recent_price - cheapestSite.recent_price)'), '=', 0);
                    break;
                default:
            }
        }


        $productBuilder->select($select);
        $recordTotal = $user->products()->count();
        $recordsFiltered = $productBuilder->count();

        $productBuilder->leftJoin('sites', 'products.product_id', '=', 'sites.product_id');
        $productBuilder->groupBy('products.product_id');
        $productBuilder->having('number_of_sites', '>', 1);

        if ($this->request->has('order')) {
            $order = array_first($this->request->get('order'));
            $orderColumn = array_get($order, 'column');
            $orderSequence = array_get($order, 'dir');
            if ($orderColumn) {
                if ($orderColumn == 'diff_ref_cheapest' && $this->request->has('reference')) {
                    if ($this->request->has('reference')) {
                        if ($this->request->has('position') && $this->request->get('position') == 'cheapest') {
                            $productBuilder = $productBuilder->orderBy('diff_second_cheapest', $orderSequence);
                        } else {
                            $productBuilder = $productBuilder->orderBy('diff_cheapest', $orderSequence);
                        }
                    } else {
                        $productBuilder = $productBuilder->orderBy('categories.category_name', $orderSequence);
                    }
                } elseif ($orderColumn == 'percent_diff_ref_cheapest') {
                    if ($this->request->has('reference')) {

                        if ($this->request->has('position') && $this->request->get('position') == 'cheapest') {
                            $productBuilder = $productBuilder->orderBy('percent_diff_second_cheapest', $orderSequence);
                        } else {
                            $productBuilder = $productBuilder->orderBy('percent_diff_cheapest', $orderSequence);
                        }
                    } else {
                        $productBuilder = $productBuilder->orderBy('categories.category_name', $orderSequence);
                    }
                } else {
                    $productBuilder = $productBuilder->orderBy($orderColumn, $orderSequence);
                }
            }
        }
        $products = $productBuilder->get();
        $position = $this->request->get('position', null);
        $fileName = "export_positioning_" . Carbon::now()->format('YmdHis');
        Excel::create($fileName, function ($excel) use ($products,$position) {
            $excel->sheet('positioning view', function ($sheet) use ($products, $position) {
                $sheet->loadView('products.positioning.export', compact(['products', 'position']));
            });
        })->download('csv');

        /*TODO use excel to format it*/

    }
}