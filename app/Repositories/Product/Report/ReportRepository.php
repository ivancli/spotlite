<?php
namespace App\Repositories\Product\Report;

use App\Contracts\Repository\Product\Report\ReportContract;
use App\Filters\QueryFilter;
use App\Models\Report;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2/10/2016
 * Time: 12:56 PM
 */
class ReportRepository implements ReportContract
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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

    public function getReports(QueryFilter $queryFilter)
    {
        $categoryReports = auth()->user()->categoryReports()->with('reportable')->filter($queryFilter)->get();
        $productReports = auth()->user()->productReports()->with('reportable')->filter($queryFilter)->get();
        $reports = $categoryReports->merge($productReports);

        if ($this->request->has('order')) {
            foreach ($this->request->get('order') as $columnAndDirection) {
                if ($columnAndDirection['dir'] == 'asc') {
                    $reports = $reports->sortBy($columnAndDirection['column'])->values();
                } else {
                    $reports = $reports->sortByDesc($columnAndDirection['column'])->values();
                }
            }
        }
        if ($this->request->has('search') && isset($this->request->get('search')['value']) && strlen($this->request->get('search')['value']) > 0) {
            $searchString = $this->request->get('search')['value'];
            $reports = $reports->filter(function ($report, $key) use ($searchString) {
                if (str_contains(strtolower($report->report_owner_type), strtolower($searchString))
                    || str_contains(strtolower($report->file_name), strtolower($searchString))
                    || str_contains(strtolower($report->file_type), strtolower($searchString))
                    || str_contains(strtolower($report->created_at), strtolower($searchString))
                ) {
                    return true;
                }
                switch ($report->file_type) {
                    case "xlsx":
                        if (str_contains(strtolower("Excel 2007-2013"), strtolower($searchString))) {
                            return true;
                        }
                        break;
                    case "pdf":
                        if (str_contains(strtolower("PDF"), strtolower($searchString))) {
                            return true;
                        }
                        break;
                    case "xls":
                        if (str_contains(strtolower("Excel 2003"), strtolower($searchString))) {
                            return true;
                        }
                        break;
                    default:
                }
                if ($report->report_owner_type == "category") {
                    return str_contains(strtolower($report->report_owner->category_name), strtolower($searchString));
                } elseif ($report->report_owner_type == "product") {
                    return str_contains(strtolower($report->report_owner->product_name), strtolower($searchString));
                }
            })->values();
        }
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->__getCategoryReportCount() + $this->__getProductReportCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $reports->count();
        } else {
            $output->recordsFiltered = $this->__getCategoryReportCount() + $this->__getProductReportCount();
        }
        $output->data = $reports->toArray();
        return $output;
    }

    private function __getCategoryReportCount()
    {
        return auth()->user()->categoryReports()->count();
    }

    private function __getProductReportCount()
    {
        return auth()->user()->productReports()->count();
    }
}