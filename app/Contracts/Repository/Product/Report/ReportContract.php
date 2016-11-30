<?php
namespace App\Contracts\Repository\Product\Report;
use App\Filters\QueryFilter;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2/10/2016
 * Time: 12:50 PM
 */
interface ReportContract
{
    public function getReports(QueryFilter $queryFilter);

    public function getReport($report_id, $fail = true);

    public function storeReport($options);

    public function updateReport($report_id, $options);

    public function deleteReport($report_id);

    public function getReportFileContent($report_id);
}