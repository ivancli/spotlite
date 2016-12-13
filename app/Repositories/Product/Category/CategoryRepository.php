<?php
namespace App\Repositories\Product\Category;

use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Filters\QueryFilter;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class CategoryRepository implements CategoryContract
{
    protected $productRepo;
    protected $request;

    public function __construct(ProductContract $productContract, Request $request)
    {
        $this->productRepo = $productContract;
        $this->request = $request;
    }

    public function getCategories()
    {
        $categories = Category::all();
        return $categories;
    }

    public function getCategory($id, $fail = true)
    {
        if ($fail === true) {
            $category = Category::findOrFail($id);
        } else {
            $category = Category::find($id);
        }
        return $category;
    }

    public function getCategoriesCount()
    {
        return auth()->user()->categories->count();
    }

    public function createCategory($options)
    {
        $greatestOrder = $this->getGreatestCategoryOrder();
        if (!is_null($greatestOrder)) {
            $options['category_order'] = $greatestOrder + 1;
        } else {
            $options['category_order'] = 1;
        }
        $category = Category::create($options);
        return $category;
    }

    public function updateCategory($id, $options)
    {
        $category = $this->getCategory($id);
        $category->update($options);
        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if (!is_null($category->products)) {
            foreach ($category->products as $product) {
                $this->productRepo->deleteProduct($product->getKey());
            }
        }
        $category->delete();
        return true;
    }

    public function lazyLoadCategories(QueryFilter $queryFilter)
    {
        $categoriesBuilder = auth()->user()->categories();
        if ($this->request->has('keyword') && !empty($this->request->get('keyword'))) {
            $keyWord = $this->request->get('keyword');
            $categoriesBuilder = $categoriesBuilder->where(function ($query) use ($keyWord) {
                $query->where('category_name', 'LIKE', "%{$keyWord}%")->orWhereHas('products', function ($query) use ($keyWord) {
                    $query->where('product_name', 'LIKE', "%{$keyWord}%")->orWhereHas('sites', function ($query) use ($keyWord) {
                        $query->where('site_url', 'LIKE', "%{$keyWord}%");
                    });
                });
            });
        }
        $categoryBuilder = $categoriesBuilder->filter($queryFilter);
        if (!$this->request->has('order')) {
            $categoryBuilder->orderBy('category_order', 'asc')->orderBy('category_id');
        }
        $categories = $categoryBuilder->get();

        $output = new \stdClass();
        $output->recordTotal = $this->getCategoriesCount();
        $output->recordFiltered = $categories->count();
        $output->categories = $categories;
        return $output;
    }

    public function getGreatestCategoryOrder()
    {
        return Category::max('category_order');
    }

    public function createSampleCategory(User $user)
    {
        return Category::create(array(
            "user_id" => $user->getKey(),
            "category_name" => "My First Category",
        ));
    }
}