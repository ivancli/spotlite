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

    public function onProfileViewed($event)
    {
        $user = $event->user;
        $this->logger->storeLog("viewed profile of user_id - {$user->getKey()}");
    }

    public function onProfileEditViewed($event)
    {
        $user = $event->user;
        $this->logger->storeLog("viewed edit profile of user_id - {$user->getKey()}");
    }

    public function onProfileUpdating($event)
    {
        $user = $event->user;
        $this->logger->storeLog("updating profile of user_id - {$user->getKey()}");
    }

    public function onProfileUpdated($event)
    {
        $user = $event->user;
        $this->logger->storeLog("updated profile of user_id - {$user->getKey()}");
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
        $events->listen(
            'App\Events\User\Profile\ProfileViewed',
            'App\Listeners\UserEventSubscriber@onProfileViewed'
        );
        $events->listen(
            'App\Events\User\Profile\ProfileEditViewed',
            'App\Listeners\UserEventSubscriber@onProfileEditViewed'
        );
        $events->listen(
            'App\Events\User\Profile\ProfileUpdating',
            'App\Listeners\UserEventSubscriber@onProfileUpdating'
        );
        $events->listen(
            'App\Events\User\Profile\ProfileUpdated',
            'App\Listeners\UserEventSubscriber@onProfileUpdated'
        );
    }

}