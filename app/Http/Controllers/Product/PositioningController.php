<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/28/2017
 * Time: 9:20 AM
 */

namespace App\Http\Controllers\Product;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositioningController extends Controller
{
    var $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    public function index()
    {
        $user = auth()->user();
        $domains = $user->sites->pluck('domain')->unique();
        /* list of categories */
        $categories = $user->categories;
        /* list of brands */
        $productMetas = $user->productMetas;
        $brands = $productMetas->pluck('brand')->unique();

        /* list of suppliers */
        $suppliers = $productMetas->pluck('supplier')->unique();
        dump($domains);
        dump($categories);
        dump($brands);
        dd($suppliers);

        return view('products.positioning.index');
    }
}