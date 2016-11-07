<?php
namespace App\Listeners\Products;

use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Jobs\LogUserActivity;


/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class SiteEventSubscriber
{
    protected $mailingAgentRepo;

    public function __construct(MailingAgentContract $mailingAgentContract)
    {
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    public function onSiteDeleting($event)
    {
        $site = $event->site;
        dispatch((new LogUserActivity(auth()->user(), "deleting site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteDeleted($event)
    {
        $site = $event->site;
        $this->mailingAgentRepo->updateNumberOfSites();
        dispatch((new LogUserActivity(auth()->user(), "deleted site - {$site->getKey()}"))->onQueue("logging"));
    }

    public function onSiteCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed create site page"))->onQueue("logging"));
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
        $this->mailingAgentRepo->updateNumberOfSites();
        $this->mailingAgentRepo->updateLastAddSiteDate();
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

    public function onSiteMyPriceSet($event)
    {
        $site = $event->site;
        $this->mailingAgentRepo->updateLastNominatedMyPriceDate();
        dispatch((new LogUserActivity(auth()->user(), "setting my price with site - {$site->getKey()}"))->onQueue("logging"));
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Products\Site\SiteDeleting',
            'App\Listeners\Products\SiteEventSubscriber@onSiteDeleting'
        );
        $events->listen(
            'App\Events\Products\Site\SiteDeleted',
            'App\Listeners\Products\SiteEventSubscriber@onSiteDeleted'
        );

        $events->listen(
            'App\Events\Products\Site\SiteCreateViewed',
            'App\Listeners\Products\SiteEventSubscriber@onSiteCreateViewed'
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
        $events->listen(
            'App\Events\Products\Site\SiteMyPriceSet',
            'App\Listeners\Products\SiteEventSubscriber@onSiteMyPriceSet'
        );

    }
}