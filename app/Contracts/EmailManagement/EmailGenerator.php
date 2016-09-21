<?php
namespace App\Contracts\EmailManagement;
use App\Models\AlertEmail;
use App\Models\User;
use DaveJamesMiller\Breadcrumbs\View;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 11:08 PM
 */
interface EmailGenerator
{
    public function sendWelcomeEmail(User $user);

    public function sendMail($view, array $data = array(), AlertEmail $alertEmail, $subject);
}