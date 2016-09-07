<?php

namespace App\Events\Products\Category;

use App\Events\Event;
use App\Models\Category;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CategoryUpdating extends Event
{
    use SerializesModels;

    public $category;

    /**
     * Create a new event instance.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
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
