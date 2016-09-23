<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 4:39 PM
 */

namespace App\Contracts\ProductManagement;


interface ReportManager
{
    public function getReport($report_id, $fail = true);

    public function storeReport($options);

    public function updateReport($report_id, $options);

    public function deleteReport($report_id);

    public function getReportFileContent($report_id);

}