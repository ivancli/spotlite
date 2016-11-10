<?php
namespace App\Contracts\Repository\Product\Category;

use App\Filters\QueryFilter;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:25 PM
 */
interface CategoryContract
{
    public function getCategories();

    public function getCategory($id, $fail = true);

    public function getCategoriesCount();

    public function createCategory($options);

    public function updateCategory($id, $options);

    public function deleteCategory($id);

    public function lazyLoadCategories(QueryFilter $queryFilter);

    public function getGreatestCategoryOrder();

    public function createSampleCategory(User $user);
}