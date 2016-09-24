<?php
namespace App\Repositories\EmailManagement;

use App\Contracts\EmailManagement\EmailGenerator;
use App\Models\AlertEmail;
use App\Models\ReportEmail;
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

//    public function sendMail($view, array $data = array(), AlertEmail $alertEmail, $subject)
//    {
//        Mail::send($view, $data, function ($m) use ($alertEmail, $subject) {
//            $m->from(config('mail.from.address'), config('mail.from.name'));
//            $m->to($alertEmail->alert_email_address)->subject($subject);
//        });
//    }
    public function sendMail($view, array $data = array(), array $options = array())
    {
        dump($options);
        Mail::send($view, $data, function ($m) use ($options) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($options['email'], (isset($options['first_name']) && isset($options['last_name'])) ? "{$options['first_name']}  {$options['last_name']}" : null)
                ->subject($options['subject']);

            if (isset($options['attachment'])) {
                $m->attachData(base64_decode($options['attachment']['data']), $options['attachment']['file_name']);
            }
        });
    }

//    public function sendReport($view, array $data = array(), ReportEmail $reportEmail, $subject, array $attachment = array())
//    {
//        Mail::send($view, $data, function ($m) use ($reportEmail, $subject, $attachment) {
//            $m->from(config('mail.from.address'), config('mail.from.name'));
//            $m->to($reportEmail->report_email_address)->subject($subject);
//
//            if (isset($attachment['data']) && $attachment['file_name']) {
//                $m->attachData($attachment['data'], $attachment['file_name']);
//            }
//        });
//    }
}