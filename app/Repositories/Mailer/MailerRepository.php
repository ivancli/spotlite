<?php
namespace App\Repositories\Mailer;

use App\Contracts\Repository\Mailer\MailerContract;
use App\Models\AlertEmail;
use App\Models\ReportEmail;
use App\Models\Unsubscriber;
use App\Models\User;
use DaveJamesMiller\Breadcrumbs\View;
use Illuminate\Support\Facades\Mail;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 11:09 PM
 */
class MailerRepository implements MailerContract
{

    public function sendWelcomeEmail(User $user)
    {
        Mail::send('auth.emails.welcome', compact(['user']), function ($m) use ($user) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($user->email, "{$user->first_name} {$user->last_name}")->subject('Welcome to SpotLite');
        });
    }

    public function sendMail($view, array $data = array(), array $options = array())
    {
        $unsubscriber = Unsubscriber::where('email', $options['email'])->first();
        if (!is_null($unsubscriber)) {
            $unsubscriber->blocked++;
            $unsubscriber->save();
            return false;
        }

        Mail::send($view, $data, function ($m) use ($options) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($options['email'], (isset($options['first_name']) && isset($options['last_name'])) ? "{$options['first_name']}  {$options['last_name']}" : null)
                ->subject($options['subject']);

            if (isset($options['attachment'])) {
                $m->attachData(base64_decode($options['attachment']['data']), $options['attachment']['file_name']);
            }
        });
    }

    public function sendToSupport($view, array $data = array(), array $options = array())
    {
        Mail::send($view, $data, function ($m) use ($options) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to(config("support.support_email_address"))
                ->subject($options['subject']);
        });
    }
}