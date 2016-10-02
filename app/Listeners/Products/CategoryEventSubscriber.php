<?php
namespace App\Listeners\Products;


use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class CategoryEventSubscriber
{
    public function onCategoryCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed category create form"))->onQueue("logging"));
    }

    public function onCategorySingleViewed($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "viewed single category - {$category->getKey()}"))->onQueue("logging"));
    }

    public function onCategoryStored($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "stored category - {$category->getKey()}"))->onQueue("logging"));
    }

    public function onCategoryStoring($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "storing category"))->onQueue("logging"));
    }

    public function onCategoryUpdated($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "updated category - {$category->getKey()}"))->onQueue("logging"));
    }

    public function onCategoryUpdating($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "updating category - {$category->getKey()}"))->onQueue("logging"));
    }

    public function onCategoryDeleting($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "deleting category - {$category->getKey()}"))->onQueue("logging"));
    }

    public function onCategoryDeleted($event)
    {
        $category = $event->category;
        dispatch((new LogUserActivity(auth()->user(), "deleted category - {$category->getKey()}"))->onQueue("logging"));
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