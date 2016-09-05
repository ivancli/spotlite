<?php

namespace App\Events\Group;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Invigor\UM\UMGroup;

class GroupEditViewed extends Event
{
    use SerializesModels;

    public $group;
    /**
     * Create a new event instance.
     */
    public function __construct(UMGroup $group)
    {
        $this->group = $group;
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
