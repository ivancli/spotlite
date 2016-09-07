<?php
namespace App\Listeners\Products;

use App\Contracts\LogManagement\Logger;

//use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class SiteEventSubscriber
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onSiteAttached($event)
    {
        $site = $event->site;
        $product = $event->product;
        $this->logger->storeLog("attached product - {$product->getKey()} and site - {$site->getKey()}");
    }

    public function onSiteCreateViewed($event)
    {
        $this->logger->storeLog("viewed create site page");
    }

    public function onSiteDetached($event)
    {
        $site = $event->site;
        $product = $event->product;
        $this->logger->storeLog("detached product - {$product->getKey()} and site - {$site->getKey()}");
    }

    public function onSiteEditViewed($event)
    {
        $site = $event->site;
        $this->logger->storeLog("viewed site edit page - {$site->getKey()}");
    }

    public function onSitePricesViewed($event)
    {
        $this->logger->storeLog("viewed site prices list");
    }

    public function onSiteSingleViewed($event)
    {
        $site = $event->site;
        $this->logger->storeLog("viewed single site - {$site->getKey()}");
    }

    public function onSiteStored($event)
    {
        $site = $event->site;
        $this->logger->storeLog("stored site - {$site->getKey()}");
    }

    public function onSiteStoring($event)
    {
        $this->logger->storeLog("storing site");
    }

    public function onSiteUpdated($event)
    {
        $site = $event->site;
        $this->logger->storeLog("updated site - {$site->getKey()}");
    }

    public function onSiteUpdating($event)
    {
        $site = $event->site;
        $this->logger->storeLog("updating site - {$site->getKey()}");
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