<?php

namespace App\Events\Products\Site;

use App\Events\Event;
use App\Models\Site;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SiteSingleViewed extends Event
{
    use SerializesModels;

    public $site;

    /**
     * Create a new event instance.
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
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
