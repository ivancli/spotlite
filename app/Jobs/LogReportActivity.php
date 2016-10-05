<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 1:40 PM
 */

namespace App\Jobs;


use App\Contracts\Repository\Logger\ReportActivityLoggerContract;
use App\Models\ReportTask;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogReportActivity
{
    use InteractsWithQueue, SerializesModels;

    protected $reportTask;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @param ReportTask $reportTask
     * @param $options
     */
    public function __construct(ReportTask $reportTask, $options)
    {
        $this->options = $options;
        $this->reportTask = $reportTask;
    }

    /**
     * Execute the job.
     * @param ReportActivityLoggerContract $reportActivityLoggerRepo
     */
    public function handle(ReportActivityLoggerContract $reportActivityLoggerRepo)
    {
        $this->options['report_task_id'] = $this->reportTask->getKey();

        $reportActivityLoggerRepo->storeLog($this->options);
    }
}