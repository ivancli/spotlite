<?php
namespace App\Repositories\ProductManagement;

use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Filters\QueryFilter;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class SLCategoryManager implements CategoryManager
{
    protected $productManager;
    protected $request;

    public function __construct(ProductManager $productManager, Request $request)
    {
        $this->productManager = $productManager;
        $this->request = $request;
    }

    public function getCategories()
    {
        $categories = Category::all();
        return $categories;
    }

    public function getCategory($id)
    {
        $category = Category::findOrFail($id);
        return $category;
    }

    public function getCategoriesCount()
    {
        return auth()->user()->categories->count();
    }

    public function createCategory($options)
    {
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
                $this->productManager->deleteProduct($product->getKey());
            }
        }
        $category->delete();
        return true;
    }

    public function lazyLoadCategories(QueryFilter $queryFilter)
    {
        $categoryBuilder = auth()->user()->categories()->filter($queryFilter);
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
}