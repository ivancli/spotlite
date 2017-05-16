<?php
namespace App\Listeners;


use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Jobs\LogUserActivity;
use Illuminate\Support\Facades\Cache;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class UserEventSubscriber
{
    protected $subscriptionRepo;
    protected $mailingAgentRepo;

    public function __construct(SubscriptionContract $subscriptionContract, MailingAgentContract $mailingAgentContract)
    {
        $this->subscriptionRepo = $subscriptionContract;
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    /**
     * Handle user login events.
     * @param $event
     */
    public function onUserLogin($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "login"))->onQueue("logging")->onConnection('sync'));

        $user = $event->user;




        $user->clearAllCache();
        if (!is_null($user->apiSubscription)) {
            Cache::tags(["subscriptions.{$user->apiSubscription->id}"])->flush();
        }
        $user->last_login = date('Y-m-d H:i:s');
        if (is_null($user->is_first_login)) {
            $user->is_first_login = 'y';
        } elseif ($user->is_first_login == 'y') {
            $user->is_first_login = 'n';
        }
        $user->save();

        if ($user->needSubscription) {
            $subscriber = $this->mailingAgentRepo->getSubscriber($user->email);
            /*if there is no subscription record in Campaign Monitor, add a new subscription record*/
            if (is_null($subscriber)) {
                $this->mailingAgentRepo->addSubscriber(array(
                    'EmailAddress' => $user->email,
                    'Name' => $user->first_name . " " . $user->last_name,
                    'CustomFields' => array(
                        array(
                            "Key" => "NumberofSites",
                            "Value" => $user->sites()->count()
                        ),
                        array(
                            "Key" => "NumberofProducts",
                            "Value" => $user->products()->count()
                        ),
                        array(
                            "Key" => "NumberofCategories",
                            "Value" => $user->categories()->count()
                        ),
                    ),
                ));
            }
        }

        $this->mailingAgentRepo->editSubscriber($user->email, array(
            'CustomFields' => array(
                array(
                    'Key' => 'LastLoginDate',
                    'Value' => date("Y/m/d"),
                ),
            )
        ));

        /*TODO disable the following line*/
        if (!is_null($user->subscription)) {
            $this->subscriptionRepo->updateCreditCardDetails($user->subscription);
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