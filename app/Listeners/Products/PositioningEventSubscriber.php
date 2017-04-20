<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/20/2017
 * Time: 2:26 PM
 */

namespace App\Listeners\Products;


use App\Jobs\LogUserActivity;

class PositioningEventSubscriber
{
    public function onBeforeIndex($event)
    {

    }

    public function onAfterIndex($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed positioning page"))->onQueue("logging")->onConnection('sync'));
    }

    public function onBeforeShow($event)
    {

    }

    public function onAfterShow($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "loaded positioning table data"))->onQueue("logging")->onConnection('sync'));
    }

    public function subscribe($events)
    {
        $events->listen('App\Events\Products\Positioning\BeforeIndex', 'App\Listeners\Products\PositioningEventSubscriber@onBeforeIndex');
        $events->listen('App\Events\Products\Positioning\AfterIndex', 'App\Listeners\Products\PositioningEventSubscriber@onAfterIndex');
        $events->listen('App\Events\Products\Positioning\BeforeShow', 'App\Listeners\Products\PositioningEventSubscriber@onBeforeShow');
        $events->listen('App\Events\Products\Positioning\AfterShow', 'App\Listeners\Products\PositioningEventSubscriber@onAfterShow');
    }
}