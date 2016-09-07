<?php
namespace App\Listeners\Products;

use App\Contracts\LogManagement\Logger;

//use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class CategoryEventSubscriber
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    public function onCategoryCreateViewed($event)
    {
        $this->logger->storeLog("viewed category create form");
    }

    public function onCategorySingleViewed($event)
    {
        $category = $event->category;
        $this->logger->storeLog("viewed single category - {$category->getKey()}");
    }

    public function onCategoryStored($event)
    {
        $category = $event->category;
        $this->logger->storeLog("stored category - {$category->getKey()}");
    }

    public function onCategoryStoring($event)
    {
        $this->logger->storeLog("storing category");
    }

    public function onCategoryUpdated($event)
    {
        $category = $event->category;
        $this->logger->storeLog("updated category - {$category->getKey()}");
    }

    public function onCategoryUpdating($event)
    {
        $category = $event->category;
        $this->logger->storeLog("updating category - {$category->getKey()}");
    }

    public function onCategoryDeleting($event)
    {
        $category = $event->category;
        $this->logger->storeLog("deleting category - {$category->getKey()}");
    }

    public function onCategoryDeleted($event)
    {
        $category = $event->category;
        $this->logger->storeLog("deleted category - {$category->getKey()}");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Products\Category\CategoryCreateViewed',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryCreateViewed'
        );
        $events->listen(
            'App\Events\Products\Category\CategorySingleViewed',
            'App\Listeners\Products\CategoryEventSubscriber@onCategorySingleViewed'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryStored',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryStored'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryStoring',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryStoring'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryUpdated',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryUpdated'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryUpdating',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryUpdating'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryDeleting',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryDeleting'
        );
        $events->listen(
            'App\Events\Products\Category\CategoryDeleted',
            'App\Listeners\Products\CategoryEventSubscriber@onCategoryDeleted'
        );
    }
}