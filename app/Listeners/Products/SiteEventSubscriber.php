<?php
namespace App\Listeners\Products;

use App\Jobs\LogUserActivity;


/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class SiteEventSubscriber
{

    public function onSiteAttached($event)
    {
        $site = $event->site;
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "attached product - {$product->getKey()} and site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed create site page"))->onQueue("logging"));
    }

    public function onSiteDetached($event)
    {
        $site = $event->site;
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "detached product - {$product->getKey()} and site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteEditViewed($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "viewed site edit page - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSitePricesViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed site prices list"))->onQueue("logging"));
    }

    public function onSiteSingleViewed($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "viewed single site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteStored($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "stored site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteStoring($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "storing site"))->onQueue("logging"));
    }

    public function onSiteUpdated($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "updated site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteUpdating($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "updating site - {$site->getKey()}"))->onQueue("logging"));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Products\Site\SiteAttached',
            'App\Listeners\Products\SiteEventSubscriber@onSiteAttached'
        );

        $events->listen(
            'App\Events\Products\Site\SiteCreateViewed',
            'App\Listeners\Products\SiteEventSubscriber@onSiteCreateViewed'
        );
        $events->listen(
            'App\Events\Products\Site\SiteDetached',
            'App\Listeners\Products\SiteEventSubscriber@onSiteDetached'
        );
        $events->listen(
            'App\Events\Products\Site\SiteEditViewed',
            'App\Listeners\Products\SiteEventSubscriber@onSiteEditViewed'
        );
        $events->listen(
            'App\Events\Products\Site\SitePricesViewed',
            'App\Listeners\Products\SiteEventSubscriber@onSitePricesViewed'
        );
        $events->listen(
            'App\Events\Products\Site\SiteSingleViewed',
            'App\Listeners\Products\SiteEventSubscriber@onSiteSingleViewed'
        );
        $events->listen(
            'App\Events\Products\Site\SiteStored',
            'App\Listeners\Products\SiteEventSubscriber@onSiteStored'
        );
        $events->listen(
            'App\Events\Products\Site\SiteStoring',
            'App\Listeners\Products\SiteEventSubscriber@onSiteStoring'
        );
        $events->listen(
            'App\Events\Products\Site\SiteUpdated',
            'App\Listeners\Products\SiteEventSubscriber@onSiteUpdated'
        );
        $events->listen(
            'App\Events\Products\Site\SiteUpdating',
            'App\Listeners\Products\SiteEventSubscriber@onSiteUpdating'
        );

    }
}