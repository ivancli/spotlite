<?php
namespace App\Listeners;


//use App\Jobs\LogUserActivity;
use App\Contracts\LogManagement\UserActivityLogger;
use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class UserEventSubscriber
{

    protected $userActivityLogger;

    public function __construct(UserActivityLogger $userActivityLogger)
    {
        $this->userActivityLogger = $userActivityLogger;
    }

    /**
     * Handle user login events.
     * @param $event
     */
    public function onUserLogin($event)
    {
        $this->userActivityLogger->storeLog("login");
        dispatch(new LogUserActivity(auth()->user(), "login"));
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
//        $this->userActivityLogger->storeLog("logout");
        dispatch(new LogUserActivity(auth()->user(), "logout"));
    }

    public function onProfileViewed($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("viewed profile of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed profile of user_id - {$user->getKey()}"));
    }

    public function onProfileEditViewed($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("viewed edit profile of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed edit profile of user_id - {$user->getKey()}"));
    }

    public function onProfileUpdating($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("updating profile of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updating profile of user_id - {$user->getKey()}"));
    }

    public function onProfileUpdated($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("updated profile of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updated profile of user_id - {$user->getKey()}"));
    }

    public function onAccountViewed($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("viewed account of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed account of user_id - {$user->getKey()}"));
    }

    public function onAccountEditViewed($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("viewed edit account of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "viewed edit account of user_id - {$user->getKey()}"));
    }

    public function onAccountUpdating($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("updating account of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updating account of user_id - {$user->getKey()}"));
    }

    public function onAccountUpdated($event)
    {
        $user = $event->user;
//        $this->userActivityLogger->storeLog("updated account of user_id - {$user->getKey()}");
        dispatch(new LogUserActivity(auth()->user(), "updated account of user_id - {$user->getKey()}"));
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
        /*Account settings related event listeners*/
        $events->listen(
            'App\Events\User\Account\AccountViewed',
            'App\Listeners\UserEventSubscriber@onAccountViewed'
        );
        $events->listen(
            'App\Events\User\Account\AccountEditViewed',
            'App\Listeners\UserEventSubscriber@onAccountEditViewed'
        );
        $events->listen(
            'App\Events\User\Account\AccountUpdating',
            'App\Listeners\UserEventSubscriber@onAccountUpdating'
        );
        $events->listen(
            'App\Events\User\Account\AccountUpdated',
            'App\Listeners\UserEventSubscriber@onAccountUpdated'
        );
    }

}