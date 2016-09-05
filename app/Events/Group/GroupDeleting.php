<?php

namespace App\Events\Group;

use App\Events\Event;
use App\Models\Group;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupDeleting extends Event
{
    use SerializesModels;

    public $group;

    /**
     * Create a new event instance.
     * @param Group $group
     */
    public function __construct(Group $group)
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
