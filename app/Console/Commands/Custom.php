<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 6/06/2017
 * Time: 9:53 AM
 */

namespace App\Console\Commands;


use App\Models\Product;
use App\Models\ProductMeta;
use App\Models\User;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class Custom extends Command
{
    protected $signature = "custom";
    protected $description = 'Pushing available crawlers to queue';

    protected $crawler = null;

    public function handle()
    {

        $products = [];
        $result = Excel::load(storage_path('import/musos_product.csv'), function ($reader) use (&$products) {
            $data = $reader->all();
            foreach ($data as $index => $product) {
                $rowNumber = $index + 2;

                $productData = $product->all();
                $products [] = $productData;
            }
        }, 'Windows-1252');

        $user = User::findOrFail(219);
        $this->output->progressStart(count($products));

        foreach ($products as $product) {
            $category_name = array_get($product, 'category');
            $product_name = array_get($product, 'product');
            $sku = array_get($product, 'sku');
            $supplier = array_get($product, 'supplier');
            $brand = array_get($product, 'brand');
            $cost_price = array_get($product, 'cost_price');

            $category = $user->categories()->where('category_name', $category_name)->first();
            if (!is_null($category)) {
                $userProduct = $category->products()->where('product_name', $product_name)->first();
                if (!is_null($userProduct)) {
                    $meta = $userProduct->meta;
                    $meta->update($product);
                }
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}