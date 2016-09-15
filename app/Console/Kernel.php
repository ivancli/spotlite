<?php

namespace App\Console;

use App\Contracts\CrawlerManagement\CrawlerManager;
use App\Jobs\CrawlSite;
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

            $crawlTimes = AppPreference::getCrawlTimes();
            $currentHour = intval(date("H"));
            if (in_array($currentHour, $crawlTimes)) {
                $crawler = $this->crawlerManager->pickCrawler();
                if (!is_null($crawler)) {
                    dispatch((new CrawlSite($crawler))->onQueue("crawling"));
                    $crawler->queue();
                }
            }
        })->everyMinute();

        $schedule->call(function () {
            /*testing*/
            $content = file_get_contents(base_path('storage/logs/') . "ivan.log");
            file_put_contents(base_path('storage/logs/') . "ivan.log", $content . "\r\n" . date('Y-m-d h:i:s'));
            return;

            $userSyncTime = AppPreference::getUserSyncTimes();
            $currentHour = intval(date("H"));
            if (in_array($currentHour, $userSyncTime)) {
                $users = User::all();
                if (!is_null($crawler)) {
                    dispatch((new CrawlSite($crawler))->onQueue("crawling"));
                    $crawler->queue();
                }
            }
        })->everyMinute()->withoutOverlapping();
        /**
         * User
         */
    }
}
