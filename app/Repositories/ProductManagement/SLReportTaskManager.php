<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 1:30 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\ReportTaskManager;
use App\Models\ReportTask;

class SLReportTaskManager implements ReportTaskManager
{

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

    }

    public function generateProductReport(ReportTask $reportTask)
    {
        $product = $reportTask->reportable;
    }
}