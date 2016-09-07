<?php

namespace App\Events\Products\Product;

use App\Events\Event;
use App\Models\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductUpdating extends Event
{
    use SerializesModels;

    public $product;

    /**
     * Create a new event instance.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
