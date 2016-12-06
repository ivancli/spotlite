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

    public function __construct(CategoryContract $categoryContract)
    {
        $this->middleware('permission:create_category', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_category', ['only' => ['show']]);
        $this->middleware('permission:reorder_category', ['only' => ['updateOrder']]);
        $this->middleware('permission:update_category', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_category', ['only' => ['destroy']]);


        $this->categoryRepo = $categoryContract;
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(StoreValidator $storeValidator, Request $request)
    {
        $input = $request->all();
        $input['user_id'] = auth()->user()->getKey();

        try {
            $storeValidator->validate($input);
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

        event(new CategoryStoring());
        $currentCategoryNames = auth()->user()->categories->pluck("category_name")->toArray();
        if (in_array($request->get('category_name'), $currentCategoryNames)) {
            $status = false;
            $errors = array("A category with the same name already exists.");
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        } else {
            $category = $this->categoryRepo->createCategory($input);
            $status = true;
            event(new CategoryStored($category));
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'category']));
                } else {
                    return compact(['status', 'category']);
                }
            } else {
                return redirect()->route('product.index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $category = $this->categoryRepo->getCategory($id);
        event(new CategorySingleViewed($category));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
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

        $category = $this->categoryRepo->updateCategory($id, $request->all());
        event(new CategoryUpdating($category));
        $status = true;
        event(new CategoryUpdated($category));
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'category']));
            } else {
                return compact(['status', 'category']);
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
                $category = $this->categoryRepo->getCategory($ord['category_id'], false);
                if (!is_null($category) && intval($ord['category_order']) != 0) {
                    $category->category_order = intval($ord['category_order']);
                    $category->save();
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
        /*TODO do we need delete event here?*/
        $category = $this->categoryRepo->getCategory($id);
        event(new CategoryDeleting($category));
        $status = $this->categoryRepo->deleteCategory($id);
        event(new CategoryDeleted($category));
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

    public function getUserSiteCredit(Request $request, $category_id)
    {
        $category = $this->categoryRepo->getCategory($category_id);
        $usage = $category->sites()->count();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['usage', 'status']));
            } else {
                return compact(['usage', 'status']);
            }
        } else {
            /*TODO implement if necessary*/
        }
    }
}