<?php
namespace App\Repositories\Product\Report;
use App\Contracts\Repository\Product\Report\ReportContract;
use App\Models\Report;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2/10/2016
 * Time: 12:56 PM
 */
class ReportRepository implements ReportContract
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