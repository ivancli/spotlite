<?php
namespace App\Repositories\EmailManagement;

use App\Contracts\EmailManagement\EmailGenerator;
use App\Models\User;
use DaveJamesMiller\Breadcrumbs\View;
use Illuminate\Support\Facades\Mail;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 11:09 PM
 */
class SpotLiteEmailGenerator implements EmailGenerator
{

    public function sendWelcomeEmail(User $user)
    {
        Mail::send('auth.emails.welcome', compact(['user']), function ($m) use ($user) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($user->email, "{$user->first_name} {$user->last_name}")->subject('Welcome to SpotLite');
        });
    }

    public function sendMail($view, array $data = array(), User $user, $subject)
    {
        Mail::send($view, $data, function ($m) use ($user) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($user->email, "{$user->first_name} {$user->last_name}")->subject('Welcome to SpotLite');
        });
    }
}