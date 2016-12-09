<?php
namespace App\Contracts\Repository\Security;
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/8/2016
 * Time: 10:17 AM
 */
interface TokenContract
{
    public function generateToken();

    public function verifyToken($token);
}