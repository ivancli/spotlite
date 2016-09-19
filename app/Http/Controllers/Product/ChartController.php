<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/19/2016
 * Time: 5:48 PM
 */

namespace App\Http\Controllers\Product;


use App\Http\Controllers\Controller;

class ChartController extends Controller
{
    public function categoryIndex()
    {
        return view('charts.category.index');
    }
}