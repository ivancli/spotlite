<?php

namespace App\Events\Products\Site;

use App\Events\Event;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SiteDetached extends Event
{
    use SerializesModels;

    public $site;
    public $product;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param Product $product
     */
    public function __construct(Site $site, Product $product)
    {
        $this->site = $site;
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
