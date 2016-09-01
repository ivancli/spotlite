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
class UserEventSubscriber
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle user login events.
     * @param $event
     * @internal param Logger $logger
     */
    public function onUserLogin($event)
    {
        $this->logger->storeLog("login");
//        dispatch(new LogUserActivity(auth()->user(), "login"));

        $user = $event->user;
        $user->last_login = date('Y-m-d H:i:s');
        if (is_null($user->is_first_login)) {
            $user->is_first_login = 'y';
        } elseif ($user->is_first_login == 'y') {
            $user->is_first_login = 'n';
        }
        $user->save();
    }

    public function onUserLogout($event)
    {
        $this->logger->storeLog("logout");
//        dispatch(new LogUserActivity(auth()->user(), "logout"));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\UserEventSubscriber@onUserLogin'
        );
        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@onUserLogout'
        );
    }

}