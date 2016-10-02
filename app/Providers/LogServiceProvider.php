<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('App\Contracts\Repository\Logger\UserActivityLoggerContract', 'App\Repositories\Logger\UserActivityLoggerRepository');
        $this->app->bind('App\Contracts\Repository\Logger\CrawlerActivityLoggerContract', 'App\Repositories\Logger\CrawlerActivityLoggerRepository');

        $this->app->when('App\Http\Controllers\Log\UserActivityLogController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\UserActivityLogFilters');
        $this->app->when('App\Models\UserActivityLog')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\UserActivityLogFilters');


        $this->app->when('App\Http\Controllers\Log\CrawlerActivityLogController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\CrawlerActivityLogFilters');
        $this->app->when('App\Models\CrawlerActivityLog')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\CrawlerActivityLogFilters');
    }
}
