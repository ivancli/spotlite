<?php
namespace App\Events\Products\Alert;

use App\Events\Event;
use App\Models\Alert;
use App\Models\AlertEmail;
use Illuminate\Queue\SerializesModels;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 9:53 AM
 */
class AlertSent extends Event
{
    use SerializesModels;

    public $alert;
    public $alertEmail;

    /**
     * Create a new event instance.
     * @param Alert $alert
     * @param AlertEmail $alertEmail
     */
    public function __construct(Alert $alert, AlertEmail $alertEmail)
    {
        $this->alert = $alert;
        $this->alertEmail = $alertEmail;
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