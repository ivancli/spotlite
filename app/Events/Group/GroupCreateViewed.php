<?php

namespace App\Events\Group;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Invigor\UM\UMGroup;

class GroupCreateViewed extends Event
{
    use SerializesModels;


    /**
     * Create a new event instance.
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
