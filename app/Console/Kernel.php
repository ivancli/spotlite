<?php

namespace App\Console;

use App\Contracts\CrawlerManagement\CrawlerManager;
use App\Jobs\CrawlSite;
use App\Jobs\SendReport;
use App\Jobs\SyncUser;
use App\Models\AppPreference;
use App\Models\ReportTask;
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
            /* check in every hour */
            $lastReservedAt = AppPreference::getReportLastReservedAt();
            $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
            $currentRoundedHours = date("Y-m-d H:00:00");
            if (AppPreference::getReportReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
                /*reserve the task*/
                AppPreference::setReportReserved();
                AppPreference::setReportLastReservedAt();

                /*LOOP THROUGH ALL REPORT TASKS AND TRIGGER THE DUE REPORT TASKS*/
                $reportTasks = ReportTask::all();

                foreach ($reportTasks as $reportTask) {
                    switch ($reportTask->frequency) {
                        case "daily":
                            //check report not yet sent today
                            $lastSentAt = date("Y-m-d 00:00:00", strtotime($reportTask->last_sent_at));
                            $currentRoundedDate = date("Y-m-d 00:00:00");

                            /*if last sent date is at least 1 day ahead current date*/
                            if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedDate) - strtotime($lastSentAt)) / 3600) > 0)) {
                                $lastSentRoundedDay = date("N", strtotime($reportTask->last_sent_at));
                                if (($lastSentRoundedDay == 6 || $lastSentRoundedDay == 7) && $reportTask->weekday_only == 'yes') {
                                    continue;
                                }
                                /*
                                 * precision set to be HOUR
                                 * replace 00 with i to increase precision to be MINUTE
                                 */
                                // check report time = current time
                                $currentRoundedMinute = date("H:00:00");
                                if ($reportTask->time == $currentRoundedMinute) {
                                    $reportTask->setLastSentStamp();
                                    dispatch((new SendReport($reportTask))->onQueue("reporting"));
                                }
                            }
                            break;
                        case "weekly":
                            // check report not yet sent this week
                            $lastSentAt = date('Y-\WW', strtotime($reportTask->last_sent_at));
                            $currentRoundedWeek = date('Y-\WW');
                            if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedWeek) - strtotime($lastSentAt)) / 3600) > 0)) {
                                $currentRoundedDay = date("N");

                                // check report day = current day
                                if ($reportTask->day == $currentRoundedDay) {
                                    $reportTask->setLastSentStamp();
                                    dispatch((new SendReport($reportTask))->onQueue("reporting"));
                                }
                            }
                            break;
                        case "monthly":
                            // check report not yet sent this month
                            $lastSentAt = date('Y-m', strtotime($reportTask->last_sent_at));
                            $currentRoundedMonth = date('Y-m');
                            if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedMonth) - strtotime($lastSentAt)) / 3600) > 0)) {

                                // check report date = current date
                                $currentRoundedDate = date("d");
                                if ($reportTask->date == $currentRoundedDate) {
                                    $reportTask->setLastSentStamp();
                                    dispatch((new SendReport($reportTask))->onQueue("reporting"));
                                }
                            }
                            break;
                        default:
                            return false;
                    }
                }
                AppPreference::setReportReserved('n');
            }
        })->everyMinute()->name("reports");
    }
}
