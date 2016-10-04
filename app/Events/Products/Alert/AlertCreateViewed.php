<?php
namespace App\Events\Products\Alert;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 9:53 AM
 */
class AlertCreateViewed extends Event
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