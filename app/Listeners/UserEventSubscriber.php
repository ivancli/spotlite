<?php
namespace App\Listeners;


use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class UserEventSubscriber
{
    protected $subscriptionRepo;

    public function __construct(SubscriptionContract $subscriptionContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
    }

    /**
     * Handle user login events.
     * @param $event
     */
    public function onUserLogin($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "login"))->onQueue("logging"));

        $user = $event->user;
        $user->last_login = date('Y-m-d H:i:s');
        if (is_null($user->is_first_login)) {
            $user->is_first_login = 'y';
        } elseif ($user->is_first_login == 'y') {
            $user->is_first_login = 'n';
        }
        $user->save();
        /*TODO disable the following line*/
        if (!is_null($user->validSubscription())) {
            $this->subscriptionRepo->updateCreditCardDetails($user->validSubscription());
        }
    }

    public function onUserLogout($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "logout"))->onQueue("logging"));
    }

    public function onProfileViewed($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "viewed profile of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onProfileEditViewed($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "viewed edit profile of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onProfileUpdating($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "updating profile of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onProfileUpdated($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "updated profile of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onAccountViewed($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "viewed account of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onAccountEditViewed($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "viewed edit account of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onAccountUpdating($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "updating account of user_id - {$user->getKey()}"))->onQueue("logging"));
    }

    public function onAccountUpdated($event)
    {
        $user = $event->user;
        dispatch((new LogUserActivity(auth()->user(), "updated account of user_id - {$user->getKey()}"))->onQueue("logging"));
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