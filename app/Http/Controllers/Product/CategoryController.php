<?php
namespace App\Http\Controllers\Product;

use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Events\Products\Category\CategoryCreateViewed;
use App\Events\Products\Category\CategoryDeleted;
use App\Events\Products\Category\CategoryDeleting;
use App\Events\Products\Category\CategorySingleViewed;
use App\Events\Products\Category\CategoryStored;
use App\Events\Products\Category\CategoryStoring;
use App\Events\Products\Category\CategoryUpdated;
use App\Events\Products\Category\CategoryUpdating;
use App\Exceptions\ValidationException;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;

use App\Validators\Product\Category\StoreValidator;
use App\Validators\Product\Category\UpdateValidator;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:23 PM
 */
class CategoryController extends Controller
{
    protected $categoryRepo;
    protected $filter;
    protected $request;

    public function __construct(CategoryContract $categoryContract, QueryFilter $filter, Request $request)
    {
        $this->middleware('permission:create_category', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_category', ['only' => ['show', 'index']]);
        $this->middleware('permission:reorder_category', ['only' => ['updateOrder']]);
        $this->middleware('permission:update_category', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_category', ['only' => ['destroy']]);

        $this->categoryRepo = $categoryContract;
        $this->filter = $filter;
        $this->request = $request;
    }

    public function index()
    {
        $data = $this->categoryRepo->lazyLoadCategories($this->filter);
        $html = "";
        foreach ($data->categories as $category) {
            $html .= view("products.category.partials.single_category")->with(compact(['category']));
        }
        $data->categoriesHTML = $html;
        $data->status = true;
        if ($this->request->wantsJson()) {
            return response()->json($data);
        } else {
            return $html;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        event(new CategoryCreateViewed());
        return view('products.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @param  \Illuminate\Http\
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator)
    {
        $input = $this->request->all();
        $input['user_id'] = auth()->user()->getKey();

        $storeValidator->validate($input);

        event(new CategoryStoring());
        $category = $this->categoryRepo->createCategory($input);
        $status = true;
        event(new CategoryStored($category));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'category']));
            } else {
                return compact(['status', 'category']);
            }
        } else {
            return redirect()->route('product.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        $category = $this->categoryRepo->getCategory($id);
        event(new CategorySingleViewed($category));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['category']));
            } else {
                return view('products.category.partials.single_category')->with(compact(['category']));
            }
        } else {
            return view('products.category.partials.single_category')->with(compact(['category']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateValidator $updateValidator
     * @param  \Illuminate\Http\
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UpdateValidator $updateValidator, $id)
    {
        $input = $this->request->all();
        $input['category_id'] = $id;
        $updateValidator->validate($input);

        $category = $this->categoryRepo->updateCategory($id, $this->request->all());
        event(new CategoryUpdating($category));
        $status = true;
        event(new CategoryUpdated($category));
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'category']));
            } else {
                return compact(['status', 'category']);
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
                $category = $this->categoryRepo->getCategory($ord['category_id'], false);
                if (!is_null($category) && intval($ord['category_order']) != 0) {
                    $category->category_order = intval($ord['category_order']);
                    $category->save();
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
     * @param
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*TODO do we need delete event here?*/
        $category = $this->categoryRepo->getCategory($id);
        event(new CategoryDeleting($category));
        $status = $this->categoryRepo->deleteCategory($id);
        event(new CategoryDeleted($category));
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

    public function getUserSiteCredit($category_id)
    {
        $category = $this->categoryRepo->getCategory($category_id);
        $usage = $category->sites()->count();
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['usage', 'status']));
            } else {
                return compact(['usage', 'status']);
            }
        } else {
            /*TODO implement if necessary*/
        }
    }
}