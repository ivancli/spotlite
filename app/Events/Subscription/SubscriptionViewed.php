<?php

namespace App\Events\Subscription;

use App\Events\Event;
use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubscriptionViewed extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     */
    public function __construct()
    {

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
