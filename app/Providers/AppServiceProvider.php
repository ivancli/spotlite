<?php

namespace App\Providers;

use App\Models\ProductSite;
use App\Models\Site;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Category;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

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

        $this->app->when('App\Http\Controllers\Crawler\SiteController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminSiteFilters');
        $this->app->when('App\Models\Site')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminSiteFilters');


        /* dynamic binding for crawler */
        $this->app->bind(CrawlerInterface::class, function ($app) {
            $siteId = $this->app->request->route('site_id');
            if (!is_null($siteId)) {
                $site = Site::findOrFail($siteId);
                if (!is_null($site->crawler)) {
                    if (!is_null($site->crawler->crawler_class)) {
                        try {
                            return $app->make('Invigor\Crawler\Repositories\Crawlers\\' . $site->crawler->crawler_class);
                        } catch (Exception $e) {

                        }
                    }
                }
            }
            return $app->make('Invigor\Crawler\Repositories\Crawlers\DefaultCrawler');
        });

        /* dynamic binding for parser */
        $this->app->bind(ParserInterface::class, function ($app) {
            $siteId = $this->app->request->route('site_id');
            if (!is_null($siteId)) {
                $site = Site::findOrFail($siteId);
                if (!is_null($site->crawler)) {
                    if (!is_null($site->crawler->crawler_class)) {
                        try {
                            return $app->make('Invigor\Crawler\Repositories\Parsers\\' . $site->crawler->parser_class);
                        } catch (Exception $e) {

                        }
                    }
                }
            }
            return $app->make('Invigor\Crawler\Repositories\Parsers\XPathParser');
        });
    }
}
