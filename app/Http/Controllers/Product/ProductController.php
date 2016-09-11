<?php
namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
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
use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Validators\Product\Product\StoreValidator;
use App\Validators\Product\Product\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:23 PM
 */
class ProductController extends Controller
{
    protected $productManager;
    protected $categoryManager;

    public function __construct(ProductManager $productManager, CategoryManager $categoryManager)
    {
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = auth()->user()->categories;
        event(new ProductListViewed());
        return view('products.index')->with(compact(['categories']));
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
            $category = $this->categoryManager->getCategory($request->get('category_id'));
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
        $product = $this->productManager->createProduct($request->all());
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
        $product = $this->productManager->getProduct($id);
        event(new ProductSingleViewed($product));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['product']));
            } else {
                return view('products.product.partials.single_product')->with(compact(['product']));
            }
        } else {
            return view('products.product.partials.single_product')->with(compact(['product']));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $product = $this->productManager->getProduct($id);
        event(new ProductUpdating($product));
        $product = $this->productManager->updateProduct($id, $request->all());
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

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $product = $this->productManager->getProduct($id);
        event(new ProductDeleting($product));
        $status = $this->productManager->deleteProduct($id);
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