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
class ProductEventSubscriber
{

    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    public function onProductCreateViewed($event)
    {
        $this->logger->storeLog("viewed product create form");
    }

    public function onProductDeleted($event)
    {
        $product = $event->product;
        $this->logger->storeLog("deleted product - {$product->getKey()}");
    }

    public function onProductDeleting($event)
    {
        $product = $event->product;
        $this->logger->storeLog("deleting product - {$product->getKey()}");
    }

    public function onProductListViewed($event)
    {
        $this->logger->storeLog("viewed product list page");
    }

    public function onProductSingleViewed($event)
    {
        $product = $event->product;
        $this->logger->storeLog("viewed single product - {$product->getKey()}");
    }

    public function onProductStored($event)
    {
        $product = $event->product;
        $this->logger->storeLog("stored product - {$product->getKey()}");
    }

    public function onProductStoring($event)
    {
        $this->logger->storeLog("storing product");
    }

    public function onProductUpdated($event)
    {
        $product = $event->product;
        $this->logger->storeLog("updated product - {$product->getKey()}");
    }

    public function onProductUpdating($event)
    {
        $product = $event->product;
        $this->logger->storeLog("updating product - {$product->getKey()}");
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