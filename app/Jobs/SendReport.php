<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 3:34 PM
 */

namespace App\Jobs;


use App\Contracts\EmailManagement\EmailGenerator;
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
     * @param EmailGenerator $emailGenerator
     */
    public function handle(ReportTaskManager $reportTaskManager, EmailGenerator $emailGenerator)
    {
        /* call manager to generate a report */
        /* can be a daily/weekly/monthly report */

        switch ($this->reportTask->report_task_owner_type) {
            case "category":
                $report = $reportTaskManager->generateCategoryReport($this->reportTask);
                $category = $this->reportTask->reportable;
                $fileName = str_replace(' ', '_', $category->category_name) . "_category_report" . "." . $this->reportTask->file_type;
                break;
            case "product":
                $report = $reportTaskManager->generateProductReport($this->reportTask);
                $product = $this->reportTask->reportable;
                $fileName = str_replace(' ', '_', $product->product_name) . "_product_report" . "." . $this->reportTask->file_type;
                break;
            default:
                $fileName = "filename.txt";
        }

        if (isset($report) && !is_null($report)) {
            $attachment = array(
                "data" => base64_decode($report->content),
                "file_name" => $fileName
            );

            foreach ($this->reportTask->emails as $email) {
                /* TODO generate email with attachment and send to user */
                $emailGenerator->sendReport("products.report.email.category", compact(['report']), $email->report_email_address, "Yo, sup man", $attachment);
            }
        }


    }
}
