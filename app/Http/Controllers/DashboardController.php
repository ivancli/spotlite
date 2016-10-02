<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route("product.index");
//        return view('dashboard.index');
    }
}