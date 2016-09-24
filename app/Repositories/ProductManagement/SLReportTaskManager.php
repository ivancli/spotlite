<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 1:30 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\ReportManager;
use App\Contracts\ProductManagement\ReportTaskManager;
use App\Models\ReportTask;
use Maatwebsite\Excel\Facades\Excel;

class SLReportTaskManager implements ReportTaskManager
{
    protected $reportManager;

    public function __construct(ReportManager $reportManager)
    {
        $this->reportManager = $reportManager;
    }

    public function getReportTasks()
    {
        $reportTasks = ReportTask::all();
    }

    public function getReportTask($report_task_id, $fail = true)
    {
        if ($fail == true) {
            $reportTask = ReportTask::findOrFail($report_task_id);
        } else {
            $reportTask = ReportTask::find($report_task_id);
        }
        return $reportTask;
    }

    public function storeReportTask($options)
    {
        $reportTask = ReportTask::create($options);
        return $reportTask;
    }

    public function updateReportTask($report_task_id, $options)
    {
        $reportTask = $this->getReportTask($report_task_id);
        $reportTask->update($options);
        return $reportTask;
    }

    public function deleteReportTask($report_task_id)
    {
        $reportTask = $this->getReportTask($report_task_id);
        $reportTask->delete();
    }

    public function generateCategoryReport(ReportTask $reportTask)
    {
        $category = $reportTask->reportable;
        $products = $category->products;


        /*TODO update the following code to generate real data and store it in $data variable*/
        $data = array();
        foreach ($products as $product) {
            $data[] = $product->toArray();
            $sites = $product->sites;
            foreach ($sites as $site) {
                $data[] = $site->toArray();
                $historicalPrices = $site->historicalPrices;
                foreach ($historicalPrices as $historicalPrice) {
                    $data[] = $historicalPrice->toArray();
                }
            }
        }
        /*TODO up to this point the $data variable should have report data in correct format*/

        $fileName = str_replace(' ', '_', $category->category_name) . "_category_report";
        $excel = Excel::create($fileName, function ($excel) use ($data, $fileName) {
            $excel->sheet("sheet_1", function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        });
        $excelFileContent = $excel->string($reportTask->file_type);
        $binaryExcelFileContent = base64_encode($excelFileContent);
        $report = $this->reportManager->storeReport(array(
            "report_task_id" => $reportTask->getKey(),
            "content" => $binaryExcelFileContent
        ));
        return $report;
    }

    public function generateProductReport(ReportTask $reportTask)
    {
        $product = $reportTask->reportable;


        /*TODO update the following code to generate real data and store it in $data variable*/
        $data = array();
        $data[] = $product->toArray();
        $sites = $product->sites;
        foreach ($sites as $site) {
            $data[] = $site->toArray();
            $historicalPrices = $site->historicalPrices;
            foreach ($historicalPrices as $historicalPrice) {
                $data[] = $historicalPrice->toArray();
            }
        }
        /*TODO up to this point the $data variable should have report data in correct format*/

        $fileName = str_replace(' ', '_', $product->product_name) . "_product_report";
        $excel = Excel::create($fileName, function ($excel) use ($data, $fileName) {
            $excel->sheet("sheet_1", function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        });
        $excelFileContent = $excel->string($reportTask->file_type);
        $binaryExcelFileContent = base64_encode($excelFileContent);
        $report = $this->reportManager->storeReport(array(
            "report_task_id" => $reportTask->getKey(),
            "content" => $binaryExcelFileContent
        ));
        return $report;
    }
}