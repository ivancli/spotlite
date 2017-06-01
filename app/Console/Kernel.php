<?php

namespace App\Console;

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
        Commands\Crawl::class,
        Commands\Report::class,
        Commands\Sync::class,
        Commands\CloneUser::class,
        Commands\FullReport::class,
        Commands\BulkImport::class,
        Commands\Alert::class,
    ];

    protected $crawler;

    public function __construct(Application $app, Dispatcher $events)
    {
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
        $schedule->command("crawl:run")->everyMinute()->name("crawl-sites");
        /**
         * Sync user task
         */
        $schedule->command("sync:run")->everyMinute()->name("sync-users");
        /**
         * Report task
         */
        $schedule->command("report:run")->everyMinute()->name("reports");
    }
}
