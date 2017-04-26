<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/20/2017
 * Time: 3:05 PM
 */
namespace App\Events\User\UserDomain;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class BeforeStore extends Event
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
