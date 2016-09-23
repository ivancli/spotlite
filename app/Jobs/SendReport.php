<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 3:34 PM
 */

namespace App\Jobs;


use App\Contracts\ProductManagement\ReportTaskManager;
use App\Models\ReportTask;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReport extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $reportTask;

    /**
     * Create a new job instance.
     * @param ReportTask $reportTask
     */
    public function __construct(ReportTask $reportTask)
    {
        $this->reportTask = $reportTask;
    }

    /**
     * Execute the job.
     * @param ReportTaskManager $reportTaskManager
     */
    public function handle(ReportTaskManager $reportTaskManager)
    {
        /* call manager to generate a report */
        /* can be a daily/weekly/monthly report */

    }
}
