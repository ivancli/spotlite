<?php
namespace App\Repositories\Security;

use App\Contracts\Repository\Security\TokenContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/8/2016
 * Time: 10:20 AM
 */
class TokenRepository implements TokenContract
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Generate Token and store in Cache
     * @return string
     */
    public function generateToken()
    {
        $token = csrf_token();
        $ip = $this->request->ip();
        Cache::put($token, bcrypt($ip . $token), 15);
        return $token;
    }

    public function verifyToken($token)
    {
        $ip = $this->request->ip();
        $cachedToken = Cache::pull($token);
        return Hash::check($ip . $token, $cachedToken);
    }
}