<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/7/2016
 * Time: 4:11 PM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class TokenController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getToken()
    {
        $token = csrf_token();
        $status = true;
        if ($this->request->has('callback')) {
            return response()->json(compact(['token', 'status']))->setCallback($this->request->get('callback'));
        } else if ($this->request->wantsJson()) {
            return response()->json(compact(['token', 'status']));
        } else {
            return compact(['token', 'status']);
        }
    }
}