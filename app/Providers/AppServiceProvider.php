<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Invigor\UM\UMGroup;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\SubscriptionManagement\SubscriptionManager', 'App\Repositories\SubscriptionManagement\ChargifySubscriptionManager');
        $this->app->bind('App\Contracts\EmailManagement\EmailGenerator', 'App\Repositories\EmailManagement\SpotLiteEmailGenerator');
        $this->app->bind('App\Contracts\GroupManagement\GroupManager', 'App\Repositories\GroupManagement\UMGroupManager');
    }
}
