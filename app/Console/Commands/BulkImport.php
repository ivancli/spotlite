<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/06/2017
 * Time: 8:50 AM
 */

namespace App\Console\Commands;


use App\Models\Category;
use App\Models\Product;
use App\Models\Site;
use App\Models\User;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class BulkImport extends Command
{
    protected $signature = "bulk-import {user_id}";
    protected $description = 'Bulk import';

    protected $crawler = null;

    public function handle()
    {
        $user_id = $this->argument('user_id');
        if (is_null($user_id)) {
            $this->output->error('user_id not found');
            return false;
        }
        $user = User::findOrFail($user_id);

        $products = collect();
        $result = Excel::load(storage_path('import/import.csv'), function ($reader) use (&$products) {
            $data = $reader->all();
            foreach ($data as $index => $product) {
                $productData = $product->all();
                $products->push($productData);
            }
        }, 'Windows-1252');

        $domains = collect();

        $this->output->progressStart($products->count());
        $products->each(function ($product) use ($user, &$domains) {
            $categoryName = array_get($product, 'category');
            $productName = array_get($product, 'product');
            $url = array_get($product, 'url');

            $category = $user->categories()->where('category_name', $categoryName)->first();
            if (is_null($category)) {
                $category = $user->categories()->save(new Category([
                    'category_name' => $categoryName,
                    'category_order' => 99999
                ]));
            }
            $product = $category->products()->where('product_name', $productName)->first();
            if (is_null($product)) {
                $product = $category->products()->save(new Product([
                    'product_name' => $productName,
                    'product_order' => 99999
                ]));
                $user->products()->save($product);
            }
            $site = $product->sites()->where('site_url', $url)->first();
            if (is_null($site)) {
                $site = $product->sites()->save(new Site([
                    'site_url' => $url
                ]));
            }
            $domains->push($site->domain);

            $this->output->progressAdvance();
        });

        $domains = $domains->unique();

        $domains->each(function ($domain) {
            $this->info($domain);
        });

        $this->output->progressFinish();
    }
}