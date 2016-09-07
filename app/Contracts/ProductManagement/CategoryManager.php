<?php
namespace App\Contracts\ProductManagement;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:25 PM
 */
interface CategoryManager
{
    public function getCategories();

    public function getCategory($id);

    public function createCategory($options);

    public function updateCategory($id, $options);

    public function deleteCategory($id);
}