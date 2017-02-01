<?php
namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Events\Products\Product\ProductCreateViewed;
use App\Events\Products\Product\ProductDeleted;
use App\Events\Products\Product\ProductDeleting;
use App\Events\Products\Product\ProductListViewed;
use App\Events\Products\Product\ProductSingleViewed;
use App\Events\Products\Product\ProductStored;
use App\Events\Products\Product\ProductStoring;
use App\Events\Products\Product\ProductUpdated;
use App\Events\Products\Product\ProductUpdating;
use App\Exceptions\ValidationException;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;

use App\Validators\Product\Product\StoreValidator;
use App\Validators\Product\Product\UpdateValidator;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:23 PM
 */
class ProductController extends Controller
{
    protected $productRepo;
    protected $categoryRepo;
    protected $request;

    public function __construct(ProductContract $productContract, CategoryContract $categoryContract, Request $request)
    {
        $this->middleware('permission:create_product', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_product', ['only' => ['show']]);
        $this->middleware('permission:reorder_product', ['only' => ['updateOrder']]);
        $this->middleware('permission:update_product', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_product', ['only' => ['destroy']]);

        $this->productRepo = $productContract;
        $this->categoryRepo = $categoryContract;
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index()
    {
        $productCount = $this->productRepo->getProductsCount();
        event(new ProductListViewed());
        return view('products.index')->with(compact(['productCount']));
    }

    public function indexByCategory($category_id)
    {
        $category = $this->categoryRepo->getCategory($category_id);
        $products = $this->productRepo->getProductsByCategory($category);
        $html = "";
        foreach ($products as $product) {
            $html .= view('products.product.partials.single_product')->with(compact(['product']));
        }
        $status = true;
        if ($this->request->wantsJson()) {
            return response()->json(compact(['html', 'status']));
        } else {
            return $html;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if ($this->request->has('category_id')) {
            $category = $this->categoryRepo->getCategory($this->request->get('category_id'));
        }
        event(new ProductCreateViewed());
        return view('products.product.create')->with(compact(['category']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator)
    {
        /*TODO add number of products validation here*/

        if (!auth()->user()->canAddProduct()) {
            $status = false;
            $errors = array("Please upgrade your subscription plan to add more products");
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        $storeValidator->validate($this->request->all());
        event(new ProductStoring());
        $product = $this->productRepo->createProduct($this->request->all());
        event(new ProductStored($product));
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'product']));
            } else {
                return compact(['status', 'product']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $product = $this->productRepo->getProduct($id);
        $status = true;
        event(new ProductSingleViewed($product));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['product', 'status']));
            } else {
                return view('products.product.partials.single_product')->with(compact(['product']));
            }
        } else {
            return view('products.product.partials.single_product')->with(compact(['product']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateValidator $updateValidator
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateValidator $updateValidator, $id)
    {
        $updateValidator->validate($this->request->all());
        $product = $this->productRepo->getProduct($id);
        event(new ProductUpdating($product));
        $product = $this->productRepo->updateProduct($id, $this->request->all());
        event(new ProductUpdated($product));
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'product']));
            } else {
                return compact(['status', 'product']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }

    public function updateOrder()
    {
        /*TODO validation here*/
        $status = false;
        if ($this->request->has('order')) {
            $order = $this->request->get('order');
            foreach ($order as $key => $ord) {
                $product = $this->productRepo->getProduct($ord['product_id'], false);
                if (!is_null($product) && intval($ord['product_order']) != 0) {
                    $product->product_order = intval($ord['product_order']);
                    $product->save();
                }
            }
            $status = true;
        }

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = $this->productRepo->getProduct($id);
        event(new ProductDeleting($product));
        $status = $this->productRepo->deleteProduct($id);
        event(new ProductDeleted($product));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }

    public function getUserProductCredit()
    {
        $total = auth()->user()->subscriptionCriteria()->product;
        $usage = auth()->user()->products()->count();
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['total', 'usage', 'status']));
            } else {
                return compact(['total', 'usage', 'status']);
            }
        } else {
            /*TODO implement if necessary*/
        }
    }

    public function getUserSiteCredit($product_id)
    {
        $product = $this->productRepo->getProduct($product_id);
        $total = auth()->user()->subscriptionCriteria()->site;
        $usage = $product->sites()->count();
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['total', 'usage', 'status']));
            } else {
                return compact(['total', 'usage', 'status']);
            }
        } else {
            /*TODO implement if necessary*/
        }
    }
}