<?php
namespace App\Http\Controllers\Product;

use App\Contracts\ProductManagement\CategoryManager;
use App\Events\Products\Category\CategoryCreateViewed;
use App\Events\Products\Category\CategoryDeleted;
use App\Events\Products\Category\CategoryDeleting;
use App\Events\Products\Category\CategorySingleViewed;
use App\Events\Products\Category\CategoryStored;
use App\Events\Products\Category\CategoryStoring;
use App\Events\Products\Category\CategoryUpdated;
use App\Events\Products\Category\CategoryUpdating;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:23 PM
 */
class CategoryController extends Controller
{
    protected $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['user_id'] = auth()->user()->getKey();
        $validator = Validator::make($input, [
            "category_name" => "required|max:255"
        ]);
        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors()->all();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
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
                    return redirect()->back()->withInput()->withErrors($validator);
                }
            } else {
                $category = $this->categoryManager->createCategory($input);
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
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $category = $this->categoryManager->getCategory($id);
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
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "category_name" => "required|max:255"
        ]);
        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors()->all();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            $category = $this->categoryManager->updateCategory($id, $request->all());
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
        $category = $this->categoryManager->getCategory($id);
        event(new CategoryDeleting($category));
        $status = $this->categoryManager->deleteCategory($id);
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
}