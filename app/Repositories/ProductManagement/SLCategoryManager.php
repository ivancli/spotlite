<?php
namespace App\Repositories\ProductManagement;

use App\Contracts\ProductManagement\CategoryManager;
use App\Models\Category;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class SLCategoryManager implements CategoryManager
{

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
        $category = Category::findOrFail($id);
        $category->update($options);
        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return true;
    }
}