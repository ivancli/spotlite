<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Site;
use App\Validators\Product\ImportProduct\StoreValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.import.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreValidator $storeValidator
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function storeProducts(StoreValidator $storeValidator)
    {
        $storeValidator->validate($this->request->all());
        $user = auth()->user();
        $file = $this->request->file('file');

        $products = [];
        $errors = [];

        /*TODO data collection and validation*/
        //import products
        $result = Excel::load($file->getPathname(), function ($reader) use (&$products, &$errors) {
            $data = $reader->all();
            foreach ($data as $index => $product) {
                $rowNumber = $index + 2;
                if (!isset($product->product_name) || is_null($product->product_name)) {
                    $errors[] = "Product name is missing in 'Import Products' row #{$rowNumber}";
                }
                if (!isset($product->category_name) || is_null($product->category_name)) {
                    $errors[] = "Category name is missing in 'Import Products' row #{$rowNumber}";
                }
                $productData = $product->all();
                $products [] = $productData;
            }
        });

        $products = collect($products);
        $product_names = $products->pluck('product_name')->all();

        if (count($errors) > 0) {
            return response(compact(['errors']), 422);
        }
        /*VALIDATION FINISHED*/
        $categoryNames = $user->categories->pluck('category_name')->all();

        $warnings = [];

        $siteCounter = 0;
        $productCounter = 0;
        $categoryCounter = 0;

        $products->each(function ($product, $index) use ($user, $categoryNames, &$warnings, &$siteCounter, &$productCounter, &$categoryCounter) {
            $rowNumber = $index + 2;
            /*IMPORT CATEGORIES*/
            $category = $user->categories()->where('category_name', $product['category_name'])->first();
            if (is_null($category)) {
                if ($this->request->has('no_new_categories') && $this->request->get('no_new_categories') == 'on') {
                    $warnings[] = "Category name in 'Import Products' row #{$rowNumber} does not exist in your account, this product and its sites were NOT imported.";
                    return true;
                } else {
                    $category = $user->categories()->save(new Category(array(
                        'category_name' => $product['category_name'],
                    )));
                    $categoryCounter++;
                }
            }
            $existingProduct = $category->products()->where('product_name', $product['product_name'])->first();
            if (is_null($existingProduct)) {
                if ($this->request->has('no_new_products') && $this->request->get('no_new_products') == 'on') {
                    $warnings[] = "Product '{$product['product_name']}' in 'Import Products' row #{$rowNumber} does not exist in your account, this product and its sites were NOT imported.";
                    return true;
                } else {
                    $existingProduct = $category->products()->save(new Product([
                        'product_name' => $product['product_name'],
                        'user_id' => $user->getKey()
                    ]));
                    $productCounter++;
                    /*TODO handle meta after merging with SPOT-336*/
                }
            }
//            foreach ($product['sites'] as $site) {
//                $existingProduct->sites()->save(new Site([
//                    'site_url' => $site['url']
//                ]));
//                $siteCounter++;
//            }
        });
        $status = true;
//
//        /*IMPORT PRODUCTS*/
        return compact(['status', 'siteCounter', 'productCounter', 'categoryCounter', 'warnings']);
//
//
//        /*IMPORT SITES*/
    }

    public function storeSite(StoreValidator $storeValidator)
    {

        $storeValidator->validate($this->request->all());
        $user = auth()->user();
        $file = $this->request->file('file');

        $products = [];
        $errors = [];

        //import sites
        Excel::selectSheets('Import Sites')->load($file->getPathname(), function ($reader) use (&$products, $product_names, &$errors) {
            $data = $reader->all();
            foreach ($data as $index => $site) {
                $rowNumber = $index + 2;
                if (!isset($site->product_name) || is_null($site->product_name)) {
                    $errors[] = "Product name is missing in 'Import Sites' row #{$rowNumber}";
                } elseif (!in_array($site->product_name, $product_names)) {
                    $errors[] = "Product '{$site->product_name}' in 'Import Sites' row #{$rowNumber} does not exist in 'Import Products'";
                }
                $products->each(function ($product, $index) use ($products, $site) {
                    if ($product['product_name'] == $site->product_name) {
                        $product['sites'][] = $site->all();
                        $products->put($index, $product);
                    }
                });

            }
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
