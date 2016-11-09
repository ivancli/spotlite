<?php
namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Mailer\MailerContract;
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

use App\Jobs\SendMail;
use App\Validators\Product\Product\StoreValidator;
use App\Validators\Product\Product\UpdateValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

    protected $filter;

    public function __construct(ProductContract $productContract, CategoryContract $categoryContract, QueryFilter $filter)
    {
        $this->productRepo = $productContract;
        $this->categoryRepo = $categoryContract;
        $this->filter = $filter;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->categoryRepo->lazyLoadCategories($this->filter);
            $html = "";
            foreach ($data->categories as $category) {
                $html .= view("products.category.partials.single_category")->with(compact(['category']));
            }
            $data->categoriesHTML = $html;
            $data->status = true;
            if ($request->wantsJson()) {
                return response()->json($data);
            } else {
                return $html;
            }
        } else {
            $categories = auth()->user()->categories;
            $productCount = $this->productRepo->getProductsCount();
            event(new ProductListViewed());
            return view('products.index')->with(compact(['categories', 'productCount']));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->has('category_id')) {
            $category = $this->categoryRepo->getCategory($request->get('category_id'));
        }
        event(new ProductCreateViewed());
        return view('products.product.create')->with(compact(['category']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator, Request $request)
    {
        /*TODO add number of products validation here*/

        if (!auth()->user()->canAddProduct()) {
            $status = false;
            $errors = array("Please upgrade your subscription plan to add more products");
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

        try {
            $storeValidator->validate($request->all());
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
        event(new ProductStoring());
        $product = $this->productRepo->createProduct($request->all());
        event(new ProductStored($product));
        $status = true;

        if ($request->ajax()) {
            if ($request->wantsJson()) {
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $product = $this->productRepo->getProduct($id);
        $status = true;
        event(new ProductSingleViewed($product));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
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
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateValidator $updateValidator, Request $request, $id)
    {
        try {
            $updateValidator->validate($request->all());
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
        $product = $this->productRepo->getProduct($id);
        event(new ProductUpdating($product));
        $product = $this->productRepo->updateProduct($id, $request->all());
        event(new ProductUpdated($product));
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'product']));
            } else {
                return compact(['status', 'product']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }

    public function updateOrder(Request $request)
    {
        /*TODO validation here*/
        $status = false;
        if ($request->has('order')) {
            $order = $request->get('order');
            foreach ($order as $key => $ord) {
                $product = $this->productRepo->getProduct($ord['product_id'], false);
                if (!is_null($product) && intval($ord['product_order']) != 0) {
                    $product->product_order = intval($ord['product_order']);
                    $product->save();
                }
            }
            $status = true;
        }

        if ($request->ajax()) {
            if ($request->wantsJson()) {
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
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $product = $this->productRepo->getProduct($id);
        event(new ProductDeleting($product));
        $status = $this->productRepo->deleteProduct($id);
        event(new ProductDeleted($product));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }
}