<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 4:39 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\ReportManager;
use App\Models\Report;

class SLReportManager implements ReportManager
{

    public function getReport($report_id, $fail = true)
    {
        if ($fail == true) {
            $report = Report::findOrFail($report_id);
        } else {
            $report = Report::find($report_id);
        }
        return $report;
    }

    public function storeReport($options)
    {
        $report = Report::create($options);
        return $report;
    }

    public function updateReport($report_id, $options)
    {
        $report = $this->getReport($report_id);
        $report->update($options);
        return $report;
    }

    public function deleteReport($report_id)
    {
        $report = $this->getReport($report_id);
        $report->delete();
        return true;
    }

    public function getReportFileContent($report_id)
    {
        $report = $this->getReport($report_id);
        if (!is_null($report->content)) {
            return base64_decode($report->content);
        }
        return false;
    }
}