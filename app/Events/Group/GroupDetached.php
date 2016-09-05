<?php

namespace App\Events\Group;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Invigor\UM\UMGroup;

class GroupDetached extends Event
{
    use SerializesModels;

    public $group;

    /**
     * Create a new event instance.
     * @param UMGroup $group
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
