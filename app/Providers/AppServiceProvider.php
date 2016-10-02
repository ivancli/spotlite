<?php

namespace App\Providers;

use App\Models\Domain;
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
        $this->app->bind('App\Contracts\Repository\Mailer\MailerContract', 'App\Repositories\Mailer\MailerRepository');
        $this->app->bind('App\Contracts\Repository\User\Group\GroupContract', 'App\Repositories\User\Group\GroupRepository');
        $this->app->bind('App\Contracts\Repository\Crawler\CrawlerContract', 'App\Repositories\Crawler\CrawlerRepository');
        $this->app->bind('App\Contracts\Repository\Subscription\SubscriptionContract', 'App\Repositories\Subscription\ChargifySubscriptionRepository');
        $this->app->bind('App\Contracts\Repository\Product\Report\ReportContract', 'App\Repositories\Product\Report\ReportRepository');
        $this->app->bind('App\Contracts\Repository\Product\Report\ReportTaskContract', 'App\Repositories\Product\Report\ReportTaskRepository');
        $this->app->bind('App\Contracts\Repository\Product\Alert\AlertContract', 'App\Repositories\Product\Alert\AlertRepository');
        $this->app->bind('App\Contracts\Repository\Product\Product\ProductContract', 'App\Repositories\Product\Product\ProductRepository');
        $this->app->bind('App\Contracts\Repository\Product\Category\CategoryContract', 'App\Repositories\Product\Category\CategoryRepository');
        $this->app->bind('App\Contracts\Repository\Product\Site\SiteContract', 'App\Repositories\Product\Site\SiteRepository');
        $this->app->bind('App\Contracts\Repository\Product\ProductSite\ProductSiteContract', 'App\Repositories\Product\ProductSite\ProductSiteRepository');
        $this->app->bind('App\Contracts\Repository\Product\Domain\DomainContract', 'App\Repositories\Product\Domain\DomainRepository');

        /* Site Query Filters */
        $this->app->when('App\Http\Controllers\Crawler\SiteController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminSiteFilters');
        $this->app->when('App\Models\Site')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminSiteFilters');

        /* Domain Query Filters */
        $this->app->when('App\Http\Controllers\Crawler\DomainController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminDomainFilters');
        $this->app->when('App\Models\Domain')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\AdminDomainFilters');

        /* Category Query Filters */
        $this->app->when('App\Http\Controllers\Product\ProductController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\CategoryFilters');
        $this->app->when('App\Models\Category')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\CategoryFilters');

        /* ReportTask Query Filters */
        $this->app->when('App\Http\Controllers\Product\ReportTaskController')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\ReportTaskFilters');
        $this->app->when('App\Models\ReportTask')
            ->needs('App\Filters\QueryFilter')
            ->give('App\Filters\ReportTaskFilters');

        /*************************************************************************
         *                                                                       *
         * CRAWLER AND PARSER CLASSES DYNAMIC BINDING BASED ON DATABASE RECORD   *
         *                                                                       *
         * ***********************************************************************
         */
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

                /*check domain settings*/
                $domain_url = parse_url($site->site_url)['host'];
                $domain = Domain::where("domain_url", $domain_url)->first();
                if (!is_null($domain)) {
                    if (!is_null($domain->crawler_class)) {
                        try {
                            return $app->make('Invigor\Crawler\Repositories\Crawlers\\' . $domain->crawler_class);
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
                    if (!is_null($site->crawler->parser_class)) {
                        try {
                            return $app->make('Invigor\Crawler\Repositories\Parsers\\' . $site->crawler->parser_class);
                        } catch (Exception $e) {

                        }
                    }
                }

                /*check domain settings*/
                $domain_url = parse_url($site->site_url)['host'];
                $domain = Domain::where("domain_url", $domain_url)->first();
                if (!is_null($domain)) {
                    if (!is_null($domain->parser_class)) {
                        try {
                            return $app->make('Invigor\Crawler\Repositories\Parsers\\' . $domain->parser_class);
                        } catch (Exception $e) {

                        }
                    }
                }
            }
            return $app->make('Invigor\Crawler\Repositories\Parsers\XPathParser');
        });
    }
}
