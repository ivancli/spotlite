<?php
namespace App\Listeners;

use App\Contracts\LogManagement\Logger;
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

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onFirstLoginViewed($event)
    {
        $this->logger->storeLog("viewed first login popup");
    }

    public function onGroupAttached($event)
    {
        $group = $event->group;
        $this->logger->storeLog("attached group - {$group->getKey()}");
    }

    public function onGroupCreateViewed($event)
    {
        $this->logger->storeLog("viewed create page of group");
    }

    public function onGroupDeleted($event)
    {
        $group = $event->group;
        $this->logger->storeLog("deleted group - {$group->getKey()}");
    }

    public function onGroupDeleting($event)
    {
        $group = $event->group;
        $this->logger->storeLog("deleting group - {$group->getKey()}");
    }

    public function onGroupDetached($event)
    {
        $group = $event->group;
        $this->logger->storeLog("detached group - {$group->getKey()}");
    }

    public function onGroupEditViewed($event)
    {
        $group = $event->group;
        $this->logger->storeLog("viewed edit page of group - {$group->getKey()}");
    }

    public function onGroupListViewed($event)
    {
        $this->logger->storeLog("viewed list page of group");
    }

    public function onGroupSingleViewed($event)
    {
        $group = $event->group;
        $this->logger->storeLog("viewed single page of group - {$group->getKey()}");
    }

    public function onGroupStored($event)
    {
        $group = $event->group;
        $this->logger->storeLog("stored group - {$group->getKey()}");
    }

    public function onGroupStoring($event)
    {
        $this->logger->storeLog("storing group");
    }

    public function onGroupUpdated($event)
    {
        $group = $event->group;
        $this->logger->storeLog("updated group - {$group->getKey()}");
    }

    public function onGroupUpdating($event)
    {
        $group = $event->group;
        $this->logger->storeLog("updating group - {$group->getKey()}");
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