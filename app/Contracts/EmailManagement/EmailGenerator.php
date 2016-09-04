<?php
namespace App\Contracts\EmailManagement;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 11:08 PM
 */
interface EmailGenerator
{
    public function sendWelcomeEmail(User $user);
}