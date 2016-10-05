<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 12:56 PM
 */

namespace App\Events\Products\Report;


use App\Events\Event;
use App\Models\Report;
use Illuminate\Queue\SerializesModels;

class ReportCreated extends Event
{
    use SerializesModels;

    public $report;

    /**
     * Create a new event instance.
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
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