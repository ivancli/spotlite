<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 1:27 PM
 */

namespace App\Contracts\Repository\Product\Report;


use App\Filters\QueryFilter;
use App\Models\ReportTask;

interface ReportTaskContract
{
    public function getReportTasks();

    public function getReportTask($report_task_id);

    public function storeReportTask($options);

    public function updateReportTask($report_task_id, $options);

    public function deleteReportTask($report_task_id);

    public function generateCategoryReport(ReportTask $reportTask);

    public function generateProductReport(ReportTask $reportTask);

    public function getDataTableReportTasks(QueryFilter $queryFilter);
}