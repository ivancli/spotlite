<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];


    protected $subscribe = [
        'App\Listeners\UserEventSubscriber',
        'App\Listeners\SubscriptionEventSubscriber',
        'App\Listeners\GroupEventSubscriber',
        'App\Listeners\Products\CategoryEventSubscriber',
        'App\Listeners\Products\ProductEventSubscriber',
        'App\Listeners\Products\SiteEventSubscriber',
        'App\Listeners\Products\CrawlerEventSubscriber',
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
