<?php
namespace App\Listeners;

use App\Contracts\LogManagement\Logger;

//use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class SubscriptionEventSubscriber
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * On subscription management page viewed
     * @param $event
     */
    public function onManagementViewed($event)
    {
        $this->logger->storeLog("viewed subscription management page");
    }

    public function onCreating($event)
    {
        $this->logger->storeLog("creating subscription");
    }

    public function onCompleted($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("completed subscription, id: {$subscription->getKey()}");
    }

    public function onEditViewed($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("viewed edit subscription page, id: {$subscription->getKey()}");
    }

    public function onUpdating($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("updating subscription, id: {$subscription->getKey()}");
    }

    public function onUpdated($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("updated subscription, id: {$subscription->getKey()}");
    }

    public function onCancelling($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("cancelling subscription, id: {$subscription->getKey()}");
    }

    public function onCancelled($event)
    {
        $subscription = $event->subscription;
        $this->logger->storeLog("cancelled subscription, id: {$subscription->getKey()}");
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Subscription\SubscriptionManagementViewed',
            'App\Listeners\SubscriptionEventSubscriber@onManagementViewed'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionCreating',
            'App\Listeners\SubscriptionEventSubscriber@onCreating'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionCompleted',
            'App\Listeners\SubscriptionEventSubscriber@onCompleted'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionEditViewed',
            'App\Listeners\SubscriptionEventSubscriber@onEditViewed'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionUpdating',
            'App\Listeners\SubscriptionEventSubscriber@onUpdating'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionUpdated',
            'App\Listeners\SubscriptionEventSubscriber@onUpdated'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionCancelling',
            'App\Listeners\SubscriptionEventSubscriber@onCancelling'
        );
        $events->listen(
            'App\Events\Subscription\SubscriptionCancelled',
            'App\Listeners\SubscriptionEventSubscriber@onCancelled'
        );
    }
}