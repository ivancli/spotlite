<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('App\Contracts\ProductManagement\CategoryManager', 'App\Repositories\ProductManagement\SLCategoryManager');
        $this->app->bind('App\Contracts\ProductManagement\ProductManager', 'App\Repositories\ProductManagement\SLProductManager');
        $this->app->bind('App\Contracts\ProductManagement\SiteManager', 'App\Repositories\ProductManagement\SLSiteManager');

    }
}
