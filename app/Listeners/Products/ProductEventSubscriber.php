<?php
namespace App\Listeners\Products;

use App\Jobs\LogUserActivity;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/30/2016
 * Time: 4:58 PM
 */
class ProductEventSubscriber
{

    public function onProductCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed product create form"))->onQueue("logging"));
    }

    public function onProductDeleted($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "deleted product - {$product->getKey()}"))->onQueue("logging"));
    }

    public function onProductDeleting($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "deleting product - {$product->getKey()}"))->onQueue("logging"));
    }

    public function onProductListViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed product list page"))->onQueue("logging"));
    }

    public function onProductSingleViewed($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "viewed single product - {$product->getKey()}"))->onQueue("logging"));
    }

    public function onProductStored($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "stored product - {$product->getKey()}"))->onQueue("logging"));
    }

    public function onProductStoring($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "storing product"))->onQueue("logging"));
    }

    public function onProductUpdated($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "updated product - {$product->getKey()}"))->onQueue("logging"));
    }

    public function onProductUpdating($event)
    {
        $product = $event->product;
        dispatch((new LogUserActivity(auth()->user(), "updating product - {$product->getKey()}"))->onQueue("logging"));
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Products\Product\ProductCreateViewed',
            'App\Listeners\Products\ProductEventSubscriber@onProductCreateViewed'
        );
        $events->listen(
            'App\Events\Products\Product\ProductDeleted',
            'App\Listeners\Products\ProductEventSubscriber@onProductDeleted'
        );
        $events->listen(
            'App\Events\Products\Product\ProductDeleting',
            'App\Listeners\Products\ProductEventSubscriber@onProductDeleting'
        );
        $events->listen(
            'App\Events\Products\Product\ProductListViewed',
            'App\Listeners\Products\ProductEventSubscriber@onProductListViewed'
        );
        $events->listen(
            'App\Events\Products\Product\ProductSingleViewed',
            'App\Listeners\Products\ProductEventSubscriber@onProductSingleViewed'
        );
        $events->listen(
            'App\Events\Products\Product\ProductStored',
            'App\Listeners\Products\ProductEventSubscriber@onProductStored'
        );
        $events->listen(
            'App\Events\Products\Product\ProductStoring',
            'App\Listeners\Products\ProductEventSubscriber@onProductStoring'
        );
        $events->listen(
            'App\Events\Products\Product\ProductUpdated',
            'App\Listeners\Products\ProductEventSubscriber@onProductUpdated'
        );
        $events->listen(
            'App\Events\Products\Product\ProductUpdating',
            'App\Listeners\Products\ProductEventSubscriber@onProductUpdating'
        );

    }
}