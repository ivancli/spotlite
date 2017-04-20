<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/20/2017
 * Time: 3:07 PM
 */
namespace App\Listeners\User;


use App\Jobs\LogUserActivity;

class UserDomainEventSubscriber
{
    public function onBeforeStore($event)
    {

    }

    public function onAfterStore($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "updated site names"))->onQueue("logging")->onConnection('sync'));
    }

    public function subscribe($events)
    {
        $events->listen('App\Events\User\UserDomain\BeforeStore', 'App\Listeners\User\UserDomainEventSubscriber@onBeforeStore');
        $events->listen('App\Events\User\UserDomain\AfterStore', 'App\Listeners\User\UserDomainEventSubscriber@onAfterStore');
    }
}