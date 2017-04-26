<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/20/2017
 * Time: 2:21 PM
 */
namespace App\Events\Products\Positioning;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class AfterShow extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     * @internal param Report $report
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