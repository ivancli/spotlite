<?php

namespace App\Console;

use App\Contracts\CrawlerManagement\CrawlerManager;
use App\Jobs\CrawlSite;
use App\Jobs\SyncUser;
use App\Models\AppPreference;
use App\Models\User;
use App\Repositories\CrawlerManagement\SLCrawlerManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    protected $crawlerManager;

    public function __construct(Application $app, Dispatcher $events, SLCrawlerManager $crawlerManager)
    {
        $this->crawlerManager = $crawlerManager;
        parent::__construct($app, $events);
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Crawling task
         */
        $schedule->call(function () {

            $lastReservedAt = AppPreference::getCrawlLastReservedAt();
            $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
            $currentRoundedHours = date("Y-m-d H:00:00");
            if (AppPreference::getCrawlReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
                /*reserve the task*/
                AppPreference::setCrawlReserved();
                AppPreference::setCrawlLastReservedAt();

                /* get the designed crawl time */
                $crawlTimes = AppPreference::getCrawlTimes();
                $currentHour = intval(date("H"));

                /* in the designed crawl time? */
                if (in_array($currentHour, $crawlTimes)) {
                    $crawlers = $this->crawlerManager->getCrawlers();

                    foreach ($crawlers as $crawler) {
                        if (is_null($crawler->last_active_at) || intval((time() - strtotime($crawler->last_active_at)) / 3600) != 0) {
                            dispatch((new CrawlSite($crawler))->onQueue("crawling"));
                            $crawler->queue();
                        } else {

                        }
                    }
                }
                AppPreference::setCrawlReserved('n');
            }
        })->everyMinute()->name("crawl-sites");


        /**
         * Sync user task
         */
        $schedule->call(function () {
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
        })->everyMinute()->name("sync-users");

        /**
         * Report task
         */

        $schedule->call(function () {
            $lastReservedAt = AppPreference::getReportLastReservedAt();
            $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
            $currentRoundedHours = date("Y-m-d H:00:00");
            if (AppPreference::getReportReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
                /*reserve the task*/
                AppPreference::setReportReserved();
                AppPreference::setReportLastReservedAt();
//                        dispatch((new ReportUser($user))->onQueue("syncing"));
                file_put_contents('/home/vagrant/Code/spotlite/storage/logs/report.log', file_get_contents('/home/vagrant/Code/spotlite/storage/logs/report.log') . "\r\n" . date('Y-m-d H:i:s') . " report called" . "\r\n");
                AppPreference::setReportReserved('n');
            }
        })->everyMinute()->name("reports");
    }
}
