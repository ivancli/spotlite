<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/7/2016
 * Time: 4:11 PM
 */

namespace App\Http\Controllers;


use App\Contracts\Repository\Security\TokenContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    protected $request;
    protected $tokenRepo;

    public function __construct(Request $request, TokenContract $tokenContract)
    {
        $this->request = $request;
        $this->tokenRepo = $tokenContract;
    }

    /**
     * TODO save the token in session and verify when accepting token
     * @return array|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getToken()
    {
        $token = $this->tokenRepo->generateToken();
        $ip = $this->request->ip();
        $key = bcrypt($ip . $token);
        $status = true;
        if ($this->request->has('callback')) {
            return response()->json(compact(['token', 'ip', 'key']))->setCallback($this->request->get('callback'));
        } else if ($this->request->wantsJson()) {
            return response()->json(compact(['token', 'ip']));
        } else {
            return compact(['token', 'ip']);
        }
    }
}