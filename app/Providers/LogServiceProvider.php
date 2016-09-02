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
        $this->app->when('App\Listeners\UserEventSubscriber')
            ->needs('App\Contracts\LogManagement\Logger')
            ->give('App\Repositories\LogManagement\UserActivityLogger');
        $this->app->when('App\Jobs\LogUserActivity')
            ->needs('App\Contracts\LogManagement\Logger')
            ->give('App\Repositories\LogManagement\UserActivityLogger');
        $this->app->when('App\Listeners\SubscriptionEventSubscriber')
            ->needs('App\Contracts\LogManagement\Logger')
            ->give('App\Repositories\LogManagement\UserActivityLogger');
        $this->app->when('App\Http\Controllers\Log\UserActivityLogController')
            ->needs('App\Contracts\LogManagement\Logger')
            ->give('App\Repositories\LogManagement\UserActivityLogger');

        $this->app->when('App\Http\Controllers\Log\UserActivityLogController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\UserActivityLogFilters');
        $this->app->when('App\Models\UserActivityLog')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\UserActivityLogFilters');
    }
}
