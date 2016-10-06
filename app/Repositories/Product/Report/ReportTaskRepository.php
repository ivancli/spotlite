<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 1:30 PM
 */

namespace App\Repositories\Product\Report;


use App\Contracts\Repository\Product\Report\ReportContract;
use App\Contracts\Repository\Product\Report\ReportTaskContract;
use App\Events\Products\Report\ReportCreated;
use App\Events\Products\Report\ReportCreating;
use App\Filters\QueryFilter;
use App\Models\ReportTask;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportTaskRepository implements ReportTaskContract
{
    protected $reportRepo;
    protected $reportTask;
    protected $request;

    public function __construct(ReportContract $reportContract, ReportTask $reportTask, Request $request)
    {
        $this->reportRepo = $reportContract;
        $this->reportTask = $reportTask;
        $this->request = $request;
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

        if (!isset($options['weekday_only'])) {
            $options['weekday_only'] = null;
        }
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
        event(new ReportCreating());
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
        $report = $this->reportRepo->storeReport(array(
            "report_task_id" => $reportTask->getKey(),
            "report_owner_type" => "category",
            "report_owner_id" => $category->getKey(),
            "content" => $binaryExcelFileContent,
            "file_name" => $fileName,
            "file_type" => $reportTask->file_type
        ));
        event(new ReportCreated($report));
        return $report;
    }

    public function generateProductReport(ReportTask $reportTask)
    {
        event(new ReportCreating());
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
        $report = $this->reportRepo->storeReport(array(
            "report_task_id" => $reportTask->getKey(),
            "report_owner_type" => "product",
            "report_owner_id" => $product->getKey(),
            "content" => $binaryExcelFileContent,
            "file_name" => $fileName,
            "file_type" => $reportTask->file_type
        ));
        event(new ReportCreated($report));
        return $report;
    }

    public function getReportTasksCount()
    {
        return auth()->user()->categoryReportTasks()->count() + auth()->user()->productReportTasks()->count();
    }

    public function getDataTableReportTasks(QueryFilter $queryFilter)
    {
        $categoryReportTasks = auth()->user()->categoryReportTasks()->with('reportable')->filter($queryFilter)->get();
        $productReportTasks = auth()->user()->productReportTasks()->with('reportable')->filter($queryFilter)->get();

        $reportTasks = $categoryReportTasks->merge($productReportTasks);

        if ($this->request->has('order')) {
            foreach ($this->request->get('order') as $columnAndDirection) {
                if ($columnAndDirection['dir'] == 'asc') {
                    $reportTasks = $reportTasks->sortBy($columnAndDirection['column'])->values();
                } else {
                    $reportTasks = $reportTasks->sortByDesc($columnAndDirection['column'])->values();
                }
            }
        }

        if ($this->request->has('search') && isset($this->request->get('search')['value']) && strlen($this->request->get('search')['value']) > 0) {
            $searchString = $this->request->get('search')['value'];
            $reportTasks = $reportTasks->filter(function ($reportTask, $key) use ($searchString) {
                if (str_contains(strtolower($reportTask->report_task_owner_type), strtolower($searchString))
                    || str_contains(strtolower($reportTask->frequency), strtolower($searchString))
                    || str_contains(strtolower($reportTask->last_sent_at), strtolower($searchString))
                ) {
                    return true;
                }
                switch ($reportTask->file_type) {
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

                if ($reportTask->report_task_owner_type == "category") {
                    return str_contains(strtolower($reportTask->report_task_owner->category_name), strtolower($searchString));
                } elseif ($reportTask->report_task_owner_type == "product") {
                    return str_contains(strtolower($reportTask->report_task_owner->product_name), strtolower($searchString));
                }
            })->values();
        }

        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getReportTasksCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $reportTasks->count();
        } else {
            $output->recordsFiltered = $this->getReportTasksCount();
        }
        $output->data = $reportTasks->toArray();
        return $output;
    }
}