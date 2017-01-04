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
     * @return bool
     */
    public function handle(ReportTaskContract $reportTaskRepo)
    {
        /* call manager to generate a report */
        /* can be a daily/weekly/monthly report */

        switch ($this->reportTask->report_task_owner_type) {
            case "category":
                $report = $reportTaskRepo->generateCategoryReport($this->reportTask);
                $category = $this->reportTask->reportable;
                if (!is_null($category)) {
                    return false;
                }
                $fileName = date("Y-m-d") . " SpotLite Category Report for {$category->category_name}" . "." . $this->reportTask->file_type;
                $subject = "{$category->category_name} Category Report for " . date("Y-m-d");
                $view = 'products.report.email.category';
                break;
            case "product":
                $report = $reportTaskRepo->generateProductReport($this->reportTask);
                $product = $this->reportTask->reportable;
                if (!is_null($product)) {
                    return false;
                }
                $subject = "{$product->product_name} Product Report for " . date("Y-m-d");
                $fileName = date("Y-m-d") . " SpotLite Product Report for {$product->product_name}" . "." . $this->reportTask->file_type;
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
                "file_name" => $fileName
            );

            foreach ($this->reportTask->emails as $email) {
                $reportTask = $this->reportTask;
                /* TODO generate email with attachment and send to user */
                event(new ReportSent($reportTask, $email));
                dispatch((new SendMail($view,
                    compact(['report', 'reportTask']),
                    array(
                        "email" => $email->report_email_address,
                        "subject" => $subject,
                        "attachment" => $attachment
                    )))->onQueue("mailing"));
            }
        }
    }
}
