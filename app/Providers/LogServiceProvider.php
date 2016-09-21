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

        $this->app->bind('App\Contracts\LogManagement\UserActivityLogger', 'App\Repositories\LogManagement\SLUserActivityLogger');
        $this->app->bind('App\Contracts\LogManagement\CrawlerActivityLogger', 'App\Repositories\LogManagement\SLCrawlerActivityLogger');

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
