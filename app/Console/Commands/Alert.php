<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/06/2017
 * Time: 11:28 AM
 */

namespace App\Console\Commands;


use App\Events\Products\Alert\AlertSent;
use App\Jobs\SendMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Alert extends Command
{
    protected $signature = "alert";
    protected $description = 'Alert';

    protected $crawler = null;

    public function handle()
    {
        $alerts = \App\Models\Alert::where('alert_owner_type', 'user')->get();

        foreach ($alerts as $alert) {

            $user = $alert->alertable;
            if (is_null($user)) {
                continue;
            }
            $lastActiveAt = $alert->last_active_at;
            if (!is_null($lastActiveAt)) {
                $lastActiveAtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $lastActiveAt);
            } else {
                $lastActiveAtCarbon = null;
            }

            $alertSites = collect();

            $this->output->progressStart($user->sites()->count());

            foreach ($user->sites as $site) {
                $lastChangedAt = $site->priceLastChangedAt;
                if (!is_null($lastChangedAt)) {
                    $lastChangedAtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $lastChangedAt);
                    if (!is_null($lastActiveAtCarbon)) {
                        if ($lastChangedAtCarbon->gt($lastActiveAtCarbon)) {
                            $alertSites->push($site);
                        }
                    }
                }
                $this->output->progressAdvance();
            }
            $this->output->progressFinish();
            if ($alertSites->count() > 0) {
                $emails = $alert->emails;
                foreach ($emails as $email) {
                    dispatch((new SendMail('products.alert.email.temp_user',
                        compact(['alertSites', 'alert']),
                        array(
                            "first_name" => $user->first_name,
                            "last_name" => $user->last_name,
                            "email" => "ivan.li@invigorgroup.com",
                            "subject" => 'SpotLite Price Alert',
                        )
                    ))->onQueue("mailing")->onConnection('sync'));

                    dispatch((new SendMail('products.alert.email.temp_user',
                        compact(['alertSites', 'alert']),
                        array(
                            "first_name" => $user->first_name,
                            "last_name" => $user->last_name,
                            "email" => $email->alert_email_address,
                            "subject" => 'SpotLite Price Alert',
                        )
                    ))->onQueue("mailing")->onConnection('sync'));

                    event(new AlertSent($alert, $email));
                }
            }
            $alert->last_active_at = date('Y-m-d H:i:s');
            $alert->save();
        }
    }
}