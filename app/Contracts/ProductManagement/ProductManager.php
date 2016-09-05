<?php
namespace App\Contracts\ProductManagement;
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:24 PM
 */
interface ProductManager
{
    public function getProducts();

    public function getProduct($id);

    public function createProduct($options);

    public function updateProduct($id, $options);

    public function deleteProduct($id);
}