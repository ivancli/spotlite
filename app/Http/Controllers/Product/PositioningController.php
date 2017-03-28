<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/28/2017
 * Time: 9:20 AM
 */

namespace App\Http\Controllers\Product;


use App\Http\Controllers\Controller;
use App\Validators\Product\Positioning\ShowValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositioningController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    public function index()
    {
        $user = auth()->user();
        $domains = $user->sites->sortBy('site_url')->pluck('domain')->unique();
        $categories = $user->categories;
        $productMetas = $user->productMetas;
        $brands = $productMetas->sortBy('brand')->pluck('brand')->unique();
        $suppliers = $productMetas->sortBy('supplier')->pluck('supplier')->unique();

        return view('products.positioning.index')->with(compact(['domains', 'categories', 'brands', 'suppliers']));
    }

    public function show(ShowValidator $showValidator)
    {
        DB::enableQueryLog();

        $showValidator->validate($this->request->all());
        $user = auth()->user();
        $productBuilder = $user->products();
        $productBuilder = DB::table('products AS products')->where('products.user_id', '=', $user->getKey());

        $select = [
            'cheapestSite.site_url as cheapest_site_url',
            'cheapestSite.recent_price as cheapest_recent_price',
            'products.*',
            'categories.*',
        ];

        $cheapestSiteQuery = DB::raw('(SELECT a.* FROM sites a JOIN (SELECT product_id, MIN(recent_price) recent_price FROM sites GROUP BY product_id) b ON(a.recent_price=b.recent_price AND a.product_id=b.product_id)) AS cheapestSite');

        $productBuilder->leftJoin($cheapestSiteQuery, function ($join) {
            $join->on('cheapestSite.product_id', '=', 'products.product_id');
        });


        if ($this->request->has('reference')) {
            $referenceDomain = $this->request->get('reference');
            $referenceQuery = DB::raw('(SELECT * FROM sites WHERE site_url LIKE "' . addslashes(urlencode($referenceDomain)) . '") AS reference');

//            $referenceQuery = DB::table('sites as reference');
//            $referenceQuery->where('reference.site_url', 'LIKE', "%{$referenceDomain}%");
//            $referenceQuery->limit(1);
            $productBuilder = $productBuilder->leftJoin($referenceQuery, function ($join) {
                $join->on('reference.product_id', '=', 'products.product_id');
            });
            $select[] = 'reference.site_url as reference_site_url';
            $select[] = 'reference.recent_price as reference_recent_price';
        }

        if ($this->request->has('category')) {
            $category = $this->request->get('category');
            $productBuilder = $productBuilder->join('categories', function ($join) use ($category) {
                $join->on('products.category_id', '=', 'categories.category_id')->where('categories.category_id', '=', $category);
            });
        } else {
            $productBuilder = $productBuilder->join('categories', 'categories.category_id', '=', 'products.category_id');
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
            $productBuilder->where('product_name', 'LIKE', "%{$keyword}%")
                ->orWhere('category_name', 'LIKE', "%{$keyword}%");
//            if ($this->request->has('reference')) {
//                $productBuilder->orWhere('reference.site_url', 'LIKE', "%{$keyword}%");
//            }
            $productBuilder->orWhere('cheapestSite.site_url', 'LIKE', "%{$keyword}%")
                ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%")
                ->orWhere('cheapestSite.recent_price', 'LIKE', "%{$keyword}%");
        }

        if ($this->request->has('order')) {
            $order = array_first($this->request->get('order'));
            $orderColumn = array_get($order, 'column');
            $orderSequence = array_get($order, 'dir');
            if ($orderColumn) {
                if ($orderColumn == 'diff_ref_cheapest') {
                    $productBuilder = $productBuilder->orderBy('ABS(reference.recent_price - cheapestSite.recent_price)');
                } elseif ($orderColumn == 'percent_diff_ref_cheapest') {

                } else {
                    $productBuilder = $productBuilder->orderBy($orderColumn, $orderSequence);
                }
            }
        }
        $productBuilder->select($select);
        $recordTotal = $user->products()->count();
        $recordsFiltered = $productBuilder->count();
        if ($this->request->has('start')) {
            $productBuilder = $productBuilder->skip($this->request->get('start'));
        }
        if ($this->request->has('length')) {
            $productBuilder = $productBuilder->take($this->request->get('length'));
        }
        $products = $productBuilder->get();
        $draw = $this->request->get('draw');
        $data = $products;


        return compact(['data', 'draw', 'recordTotal', 'recordsFiltered']);
    }
}