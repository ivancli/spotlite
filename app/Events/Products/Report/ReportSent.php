<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 12:57 PM
 */

namespace App\Events\Products\Report;


use App\Events\Event;
use App\Models\Report;
use App\Models\ReportEmail;
use App\Models\ReportTask;
use Illuminate\Queue\SerializesModels;

class ReportSent extends Event
{
    use SerializesModels;

    public $reportTask;
    public $reportEmail;

    /**
     * Create a new event instance.
     * @param ReportTask $reportTask
     * @param ReportEmail $reportEmail
     */
    public function __construct(ReportTask $reportTask, ReportEmail $reportEmail)
    {
        $this->reportTask = $reportTask;
        $this->reportEmail = $reportEmail;
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