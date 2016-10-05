<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 10:23 AM
 */

namespace App\Listeners\Products;


use App\Jobs\LogAlertActivity;
use App\Jobs\LogUserActivity;

class AlertEventSubscriber
{
    public function onAlertCreated($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "created alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "view create alert"))->onQueue("logging"));
    }

    public function onAlertCreating($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "creating alert"))->onQueue("logging"));
    }

    public function onAlertDeleted($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "deleted alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertDeleting($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "deleting alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertEdited($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "edited alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertEditing($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "editing alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertEditViewed($event)
    {
        $alert = $event->alert;
        dispatch((new LogUserActivity(auth()->user(), "viewed edit alert - {$alert->getKey()}"))->onQueue("logging"));
    }

    public function onAlertListViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed alert list page"))->onQueue("logging"));
    }

    public function onAlertSent($event)
    {
        $alert = $event->alert;
        $email = $event->alertEmail;
        dispatch((new LogAlertActivity($alert, array("type" => "sent", "email" => $email)))->onQueue("logging"));
    }

    public function onAlertTriggered($event)
    {
        $alert = $event->alert;
        dispatch((new LogAlertActivity($alert, array(
            "type" => "trigger",
        )))->onQueue("logging"));
    }

    public function subscribe($events)
    {
        $events->listen('App\Events\Products\Alert\AlertCreated', 'App\Listeners\Products\AlertEventSubscriber@onAlertCreated');
        $events->listen('App\Events\Products\Alert\AlertCreateViewed', 'App\Listeners\Products\AlertEventSubscriber@onAlertCreateViewed');
        $events->listen('App\Events\Products\Alert\AlertCreating', 'App\Listeners\Products\AlertEventSubscriber@onAlertCreating');
        $events->listen('App\Events\Products\Alert\AlertDeleted', 'App\Listeners\Products\AlertEventSubscriber@onAlertDeleted');
        $events->listen('App\Events\Products\Alert\AlertDeleting', 'App\Listeners\Products\AlertEventSubscriber@onAlertDeleting');
        $events->listen('App\Events\Products\Alert\AlertEdited', 'App\Listeners\Products\AlertEventSubscriber@onAlertEdited');
        $events->listen('App\Events\Products\Alert\AlertEditing', 'App\Listeners\Products\AlertEventSubscriber@onAlertEditing');
        $events->listen('App\Events\Products\Alert\AlertEditViewed', 'App\Listeners\Products\AlertEventSubscriber@onAlertEditViewed');
        $events->listen('App\Events\Products\Alert\AlertListViewed', 'App\Listeners\Products\AlertEventSubscriber@onAlertListViewed');
        $events->listen('App\Events\Products\Alert\AlertSent', 'App\Listeners\Products\AlertEventSubscriber@onAlertSent');
        $events->listen('App\Events\Products\Alert\AlertTriggered', 'App\Listeners\Products\AlertEventSubscriber@onAlertTriggered');
    }
}