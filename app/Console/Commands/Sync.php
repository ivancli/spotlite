<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2016
 * Time: 3:44 PM
 */

namespace App\Console\Commands;


use App\Jobs\SyncUser;
use App\Models\AppPreference;
use App\Models\User;
use Illuminate\Console\Command;

class Sync extends Command
{
    protected $signature = "sync:run";

    protected $crawler = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $lastReservedAt = AppPreference::getSyncLastReservedAt();
        $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
        $currentRoundedHours = date("Y-m-d H:00:00");
        if (AppPreference::getSyncReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
            /*reserve the task*/
            AppPreference::setSyncReserved();
            AppPreference::setSyncLastReservedAt();

            $userSyncTime = AppPreference::getSyncTimes();
            $currentHour = intval(date("H"));
            if (in_array($currentHour, $userSyncTime)) {
                $users = User::all();
                foreach ($users as $user) {
                    dispatch((new SyncUser($user))->onQueue("syncing"));
                }
            }
            AppPreference::setSyncReserved('n');
        }
    }
}