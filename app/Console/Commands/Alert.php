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
            if ($alert->getKey() != 152) {
                continue;
            }

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


            $emails = $alert->emails;
            foreach ($emails as $email) {
                dispatch((new SendMail('products.alert.email.temp_user',
                    compact(['alertSites', 'alert']),
                    array(
                        "email" => "ivan.li@invigorgroup.com",
                        "subject" => 'SpotLite Price Alert',
                    )
                ))->onQueue("mailing")->onConnection('sync'));

                event(new AlertSent($alert, $email));
            }
            $alert->last_active_at = date('Y-m-d H:i:s');
            $alert->save();
        }
    }
}