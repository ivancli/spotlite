<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/20/2017
 * Time: 2:42 PM
 */

namespace App\Listeners\Products;


use App\Jobs\LogUserActivity;

class ImportEventSubscriber
{

    public function onBeforeStoreProducts($event)
    {

    }

    public function onAfterStoreProducts($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "imported categories and products"))->onQueue("logging")->onConnection('sync'));
    }

    public function onBeforeStoreSites($event)
    {

    }

    public function onAfterStoreSites($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "imported URLs"))->onQueue("logging")->onConnection('sync'));
    }

    public function subscribe($events)
    {
        $events->listen('App\Events\Products\Import\BeforeStoreProducts', 'App\Listeners\Products\ImportEventSubscriber@onBeforeStoreProducts');
        $events->listen('App\Events\Products\Import\AfterStoreProducts', 'App\Listeners\Products\ImportEventSubscriber@onAfterStoreProducts');
        $events->listen('App\Events\Products\Import\BeforeStoreSites', 'App\Listeners\Products\ImportEventSubscriber@onBeforeStoreSites');
        $events->listen('App\Events\Products\Import\AfterStoreSites', 'App\Listeners\Products\ImportEventSubscriber@onAfterStoreSites');
    }
}