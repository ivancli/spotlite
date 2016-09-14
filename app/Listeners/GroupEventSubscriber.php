<?php
namespace App\Listeners;

use App\Contracts\LogManagement\UserActivityLogger;
use App\Jobs\LogUserActivity;
use App\Models\DeletedRecordModels\DeletedGroup;

//use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class GroupEventSubscriber
{

    protected $userActivityLogger;

    public function __construct(UserActivityLogger $userActivityLogger)
    {
        $this->userActivityLogger = $userActivityLogger;
    }

    public function onFirstLoginViewed($event)
    {
//        $this->userActivityLogger->storeLog("viewed first login popup");
        dispatch((new LogUserActivity(auth()->user(), "viewed first login popup")))->onQueue("logging");}

    public function onGroupAttached($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("attached group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "attached group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupCreateViewed($event)
    {
//        $this->userActivityLogger->storeLog("viewed create page of group");
        dispatch((new LogUserActivity(auth()->user(), "viewed create page of group"))->onQueue("logging"));
    }

    public function onGroupDeleted($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("deleted group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "deleted group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupDeleting($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("deleting group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "deleting group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupDetached($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("detached group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "detached group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupEditViewed($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("viewed edit page of group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "viewed edit page of group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupListViewed($event)
    {
//        $this->userActivityLogger->storeLog("viewed list page of group");
        dispatch((new LogUserActivity(auth()->user(), "viewed list page of group"))->onQueue("logging"));
    }

    public function onGroupSingleViewed($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("viewed single page of group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "viewed single page of group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupStored($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("stored group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "stored group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupStoring($event)
    {
//        $this->userActivityLogger->storeLog("storing group");
        dispatch((new LogUserActivity(auth()->user(), "storing group"))->onQueue("logging"));
    }

    public function onGroupUpdated($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("updated group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "updated group - {$group->getKey()}"))->onQueue("logging"));
    }

    public function onGroupUpdating($event)
    {
        $group = $event->group;
//        $this->userActivityLogger->storeLog("updating group - {$group->getKey()}");
        dispatch((new LogUserActivity(auth()->user(), "updating group - {$group->getKey()}"))->onQueue("logging"));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Group\FirstLoginViewed',
            'App\Listeners\GroupEventSubscriber@onFirstLoginViewed'
        );
        $events->listen(
            'App\Events\Group\GroupAttached',
            'App\Listeners\GroupEventSubscriber@onGroupAttached'
        );
        $events->listen(
            'App\Events\Group\GroupCreateViewed',
            'App\Listeners\GroupEventSubscriber@onGroupCreateViewed'
        );
        $events->listen(
            'App\Events\Group\GroupDeleted',
            'App\Listeners\GroupEventSubscriber@onGroupDeleted'
        );
        $events->listen(
            'App\Events\Group\GroupDeleting',
            'App\Listeners\GroupEventSubscriber@onGroupDeleting'
        );
        $events->listen(
            'App\Events\Group\GroupDetached',
            'App\Listeners\GroupEventSubscriber@onGroupDetached'
        );
        $events->listen(
            'App\Events\Group\GroupEditViewed',
            'App\Listeners\GroupEventSubscriber@onGroupEditViewed'
        );
        $events->listen(
            'App\Events\Group\GroupListViewed',
            'App\Listeners\GroupEventSubscriber@onGroupListViewed'
        );
        $events->listen(
            'App\Events\Group\GroupSingleViewed',
            'App\Listeners\GroupEventSubscriber@onGroupSingleViewed'
        );
        $events->listen(
            'App\Events\Group\GroupStored',
            'App\Listeners\GroupEventSubscriber@onGroupStored'
        );
        $events->listen(
            'App\Events\Group\GroupStoring',
            'App\Listeners\GroupEventSubscriber@onGroupStoring'
        );
        $events->listen(
            'App\Events\Group\GroupUpdated',
            'App\Listeners\GroupEventSubscriber@onGroupUpdated'
        );
        $events->listen(
            'App\Events\Group\GroupUpdating',
            'App\Listeners\GroupEventSubscriber@onGroupUpdating'
        );
    }

}