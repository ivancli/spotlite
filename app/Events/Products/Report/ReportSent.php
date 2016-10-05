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
use Illuminate\Queue\SerializesModels;

class ReportSent extends Event
{
    use SerializesModels;

    public $report;
    public $reportEmail;

    /**
     * Create a new event instance.
     * @param Report $report
     * @param ReportEmail $reportEmail
     */
    public function __construct(Report $report, ReportEmail $reportEmail)
    {
        $this->report = $report;
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