<?php

namespace App\Providers;

use App\Models\ProductSite;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'product' => Product::class,
            'category' => Category::class,
            'product_site' => ProductSite::class
        ]);
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
        $this->app->bind('App\Contracts\ProductManagement\ProductSiteManager', 'App\Repositories\ProductManagement\SLProductSiteManager');
        $this->app->bind('App\Contracts\ProductManagement\AlertManager', 'App\Repositories\ProductManagement\SLAlertManager');

        $this->app->when('App\Http\Controllers\Crawler\ProductSiteController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminProductSiteFilter');
        $this->app->when('App\Models\ProductSite')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminProductSiteFilter');
    }
}
