<?php
namespace App\Repositories\Product\Product;

use App\Contracts\Repository\Product\Product\ProductContract;
use App\Models\Category;
use App\Models\Product;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:26 PM
 */
class ProductRepository implements ProductContract
{
    public function getProducts()
    {
        $products = Product::all();
        return $products;
    }

    public function getProduct($id, $fail = true)
    {
        if ($fail === true) {
            $product = Product::findOrFail($id);
        } else {
            $product = Product::find($id);
        }
        return $product;
    }

    public function createProduct($options)
    {
        $options['user_id'] = auth()->user()->getKey();
        $product = Product::create($options);
        return $product;
    }

    public function updateProduct($id, $options)
    {
        $product = $this->getProduct($id);
        $product->update($options);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }

    public function getProductsCount()
    {
        return auth()->user()->products->count();
    }

    public function createSampleProduct(Category $category)
    {
        return Product::create(array(
            "product_name" => "My First Product",
            "category_id" => $category->getKey(),
            "user_id" => $category->user_id,
        ));
    }
}