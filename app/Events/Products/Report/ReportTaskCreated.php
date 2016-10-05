<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 11:59 AM
 */

namespace App\Events\Products\Report;


use App\Events\Event;
use App\Models\ReportTask;
use Illuminate\Queue\SerializesModels;

class ReportTaskCreated extends Event
{
    use SerializesModels;

    public $reportTask;

    /**
     * Create a new event instance.
     * @param ReportTask $reportTask
     */
    public function __construct(ReportTask $reportTask)
    {
        $this->reportTask = $reportTask;
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