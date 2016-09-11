<?php
namespace App\Listeners\Products;

use App\Contracts\LogManagement\UserActivityLogger;
use App\Jobs\LogUserActivity;

//use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class SiteEventSubscriber
{

    protected $userActivityLogger;

    public function __construct(UserActivityLogger $userActivityLogger)
    {
        $this->userActivityLogger = $userActivityLogger;
    }

    public function onSiteAttached($event)
    {
        $site = $event->site;
        $product = $event->product;
//        $this->userActivityLogger->storeLog("attached product - {$product->getKey()} and site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "attached product - {$product->getKey()} and site - {$site->getKey()}"));
    }

    public function onSiteCreateViewed($event)
    {
//        $this->userActivityLogger->storeLog("viewed create site page");
        dispatch(new LogUserActivity(auth()->user(), "viewed create site page"));
    }

    public function onSiteDetached($event)
    {
        $site = $event->site;
        $product = $event->product;
//        $this->userActivityLogger->storeLog("detached product - {$product->getKey()} and site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "detached product - {$product->getKey()} and site - {$site->getKey()}"));
    }

    public function onSiteEditViewed($event)
    {
        $site = $event->site;
//        $this->userActivityLogger->storeLog("viewed site edit page - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed site edit page - {$site->getKey()}"));
    }

    public function onSitePricesViewed($event)
    {
//        $this->userActivityLogger->storeLog("viewed site prices list");
        dispatch(new LogUserActivity(auth()->user(), "viewed site prices list"));
    }

    public function onSiteSingleViewed($event)
    {
        $site = $event->site;
//        $this->userActivityLogger->storeLog("viewed single site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed single site - {$site->getKey()}"));
    }

    public function onSiteStored($event)
    {
        $site = $event->site;
//        $this->userActivityLogger->storeLog("stored site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "stored site - {$site->getKey()}"));
    }

    public function onSiteStoring($event)
    {
//        $this->userActivityLogger->storeLog("storing site");
        dispatch(new LogUserActivity(auth()->user(), "storing site"));
    }

    public function onSiteUpdated($event)
    {
        $site = $event->site;
//        $this->userActivityLogger->storeLog("updated site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updated site - {$site->getKey()}"));
    }

    public function onSiteUpdating($event)
    {
        $site = $event->site;
//        $this->userActivityLogger->storeLog("updating site - {$site->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updating site - {$site->getKey()}"));
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