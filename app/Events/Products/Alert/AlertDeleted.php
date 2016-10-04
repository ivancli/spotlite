<?php
namespace App\Events\Products\Alert;
use App\Events\Event;
use App\Models\Alert;
use Illuminate\Queue\SerializesModels;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 9:53 AM
 */
class AlertDeleted extends Event
{
    use SerializesModels;

    public $alert;

    /**
     * Create a new event instance.
     * @param Alert $alert
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
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