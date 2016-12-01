<?php
namespace App\Repositories\User\User;

use App\Contracts\Repository\User\User\UserContract;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/12/2016
 * Time: 5:00 PM
 */
class UserRepository implements UserContract
{

    public function sampleUser()
    {
        $sampleUser = User::where("email", 'admin@spotlite.com.au')->first();
        return $sampleUser;
    }
}