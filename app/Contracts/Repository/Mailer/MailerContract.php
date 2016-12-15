<?php
namespace App\Contracts\Repository\Mailer;

use App\Models\User;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2016
 * Time: 9:37 PM
 */
interface MailerContract
{
    public function sendWelcomeEmail(User $user);

    public function sendMail($view, array $data = array(), array $options = array());

    public function sendToSupport($view, array $data = array(), array $options = array());
}