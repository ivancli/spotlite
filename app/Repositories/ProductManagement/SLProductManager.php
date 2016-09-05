<?php
namespace App\Repositories\ProductManagement;

use App\Contracts\ProductManagement\ProductManager;
use App\Models\Product;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class SLProductManager implements ProductManager
{
    public function getProducts()
    {
        $products = Product::all();
        return $products;
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return $product;
    }

    public function createProduct($options)
    {
        $product = Product::create($options);
        return $product;
    }

    public function updateProduct($id, $options)
    {
        $product = Product::findOrFail($id);
        $product->update($options);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }
}