<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 3:34 PM
 */

namespace App\Jobs;


use App\Contracts\Repository\Product\Report\ReportTaskContract;
use App\Events\Products\Report\ReportSent;
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
     * @param ReportTaskContract $reportTaskRepo
     */
    public function handle(ReportTaskContract $reportTaskRepo)
    {
        /* call manager to generate a report */
        /* can be a daily/weekly/monthly report */

        switch ($this->reportTask->report_task_owner_type) {
            case "category":
                $report = $reportTaskRepo->generateCategoryReport($this->reportTask);
                $category = $this->reportTask->reportable;
                $fileName = str_replace(' ', '_', $category->category_name) . "_category_report" . "." . $this->reportTask->file_type;
                $subject = "SpotLite " . ucfirst($category->category_name) . " " . ucfirst($this->reportTask->frequency) . " " . " Report";
                $view = 'products.report.email.category';
                break;
            case "product":
                $report = $reportTaskRepo->generateProductReport($this->reportTask);
                $product = $this->reportTask->reportable;
                $subject = "SpotLite " . ucfirst($product->product_name) . " " . ucfirst($this->reportTask->frequency) . " " . " Report";
                $fileName = str_replace(' ', '_', $product->product_name) . "_product_report" . "." . $this->reportTask->file_type;
                $view = 'products.report.email.product';
                break;
            default:
                $fileName = "filename.txt";
                $subject = "";
                $view = '';
        }

        if (isset($report) && !is_null($report)) {
            $attachment = array(
                "data" => $report->content,
                "file_name" => date("Ymd") . "_" . $fileName
            );

            foreach ($this->reportTask->emails as $email) {
                $reportTask = $this->reportTask;
                /* TODO generate email with attachment and send to user */
                event(new ReportSent($report, $email));
                dispatch((new SendMail($view,
                    compact(['report', 'reportTask']),
                    array(
                        "email" => $email->report_email_address,
                        "subject" => "SpotLite - $subject {$this->reportTask->report_task_owner_type} report",
                        "attachment" => $attachment
                    )))->onQueue("mailing"));
            }
        }


    }
}
