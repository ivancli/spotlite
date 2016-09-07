<?php
namespace App\Repositories\ProductManagement;

use App\Contracts\ProductManagement\CategoryManager;
use App\Contracts\ProductManagement\ProductManager;
use App\Models\Category;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class SLCategoryManager implements CategoryManager
{
    protected $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
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
}