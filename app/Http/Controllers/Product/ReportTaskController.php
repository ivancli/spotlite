<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/22/2016
 * Time: 5:23 PM
 */

namespace App\Http\Controllers\Product;


use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Report\ReportTaskContract;
use App\Events\Products\Report\ReportListViewed;
use App\Events\Products\Report\ReportTaskCreated;
use App\Events\Products\Report\ReportTaskCreateViewed;
use App\Events\Products\Report\ReportTaskCreating;
use App\Events\Products\Report\ReportTaskDeleted;
use App\Events\Products\Report\ReportTaskDeleting;
use App\Events\Products\Report\ReportTaskEdited;
use App\Events\Products\Report\ReportTaskEditing;
use App\Events\Products\Report\ReportTaskEditViewed;
use App\Exceptions\ValidationException;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Models\ReportEmail;
use App\Validators\Product\ReportTask\UpdateCategoryReportValidator;
use App\Validators\Product\ReportTask\UpdateProductReportValidator;
use Illuminate\Http\Request;

class ReportTaskController extends Controller
{
    protected $categoryRepo;
    protected $productRepo;
    protected $reportTaskRepo;
    protected $updateCategoryReportValidator;
    protected $updateProductReportValidator;
    protected $queryFilter;

    public function __construct(CategoryContract $categoryContract,
                                ProductContract $productContract,
                                ReportTaskContract $reportTaskContract,
                                UpdateCategoryReportValidator $updateCategoryReportValidator,
                                UpdateProductReportValidator $updateProductReportValidator,
                                QueryFilter $queryFilter)
    {
        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;
        $this->reportTaskRepo = $reportTaskContract;
        $this->updateCategoryReportValidator = $updateCategoryReportValidator;
        $this->updateProductReportValidator = $updateProductReportValidator;
        $this->queryFilter = $queryFilter;
    }

    public function index(Request $request)
    {
        event(new ReportListViewed());

        $reportTasks = $this->reportTaskRepo->getDataTableReportTasks($this->queryFilter);
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json($reportTasks);
            } else {
                return $reportTasks;
            }
        } else {
            /*TODO return a view with collection of report tasks if necessary*/
        }
    }


    /**
     * Show Edit Category Report Popup
     *
     * @param Request $request
     * @param $category_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCategoryReport(Request $request, $category_id)
    {
        $category = $this->categoryRepo->getCategory($category_id);
        if (!is_null($category->reportTask)) {
            event(new ReportTaskEditViewed($category->reportTask));
            $emails = $category->reportTask->emails->pluck('report_email_address', 'report_email_address')->toArray();
        } else {
            event(new ReportTaskCreateViewed());
            $emails = array();
        }
        return view('products.report.category')->with(compact(['category', 'emails']));
    }

    /**
     * Update Category Report Settings
     *
     * @param Request $request
     * @param $category_id
     * @return array|bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateCategoryReport(Request $request, $category_id)
    {
        try {
            $this->updateCategoryReportValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        if ($request->get('report_task_owner_id') != $category_id) {
            abort(404);
            return false;
        }
        if ($request->get('report_task_owner_type') != 'category') {
            abort(404);
            return false;
        }

        $category = $this->categoryRepo->getCategory($category_id);

        if (is_null($category->reportTask)) {

            event(new ReportTaskCreating());

            $reportTask = $this->reportTaskRepo->storeReportTask($request->all());

            event(new ReportTaskCreated($reportTask));

        } else {
            $reportTask = $category->reportTask;
            event(new ReportTaskEditing($reportTask));
            $this->reportTaskRepo->updateReportTask($reportTask->getKey(), $request->all());
            event(new ReportTaskEdited($reportTask));
        }

        $reportEmails = array();
        foreach ($reportTask->emails as $email) {
            $email->delete();
        }
        if ($request->has('email')) {
            foreach ($request->get('email') as $email) {
                $reportEmail = ReportEmail::create(array(
                    "report_task_id" => $reportTask->getKey(),
                    "report_email_address" => $email
                ));
                $reportEmails[] = $reportEmail;
            }
        }

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'reportTask', 'reportEmails']));
            } else {
                return compact(['status', 'reportTask', 'reportEmails']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    public function deleteCategoryReport(Request $request, $category_id)
    {
        $category = $this->categoryRepo->getCategory($category_id);

        if (!is_null($category->reportTask)) {
            event(new ReportTaskDeleting($category->reportTask));
            $this->reportTaskRepo->deleteReportTask($category->reportTask->getKey());
            event(new ReportTaskDeleted($category->reportTask));
        }
        $status = true;

        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    /**
     * Show Edit Product Report Popup
     *
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProductReport(Request $request, $product_id)
    {
        $product = $this->productRepo->getProduct($product_id);
        if (!is_null($product->reportTask)) {
            event(new ReportTaskEditViewed($product->reportTask));
            $emails = $product->reportTask->emails->pluck('report_email_address', 'report_email_address')->toArray();
        } else {
            event(new ReportTaskCreateViewed());
            $emails = array();
        }
        return view('products.report.product')->with(compact(['product', 'emails']));
    }

    public function updateProductReport(Request $request, $product_id)
    {
        try {
            $this->updateProductReportValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        if ($request->get('report_task_owner_id') != $product_id) {
            abort(404);
            return false;
        }
        if ($request->get('report_task_owner_type') != 'product') {
            abort(404);
            return false;
        }

        $product = $this->productRepo->getProduct($product_id);

        if (is_null($product->reportTask)) {
            event(new ReportTaskCreating());
            $reportTask = $this->reportTaskRepo->storeReportTask($request->all());
            event(new ReportTaskCreated($reportTask));
        } else {
            $reportTask = $product->reportTask;
            event(new ReportTaskEditing($reportTask));
            $this->reportTaskRepo->updateReportTask($reportTask->getKey(), $request->all());
            event(new ReportTaskEdited($reportTask));
        }

        $reportEmails = array();
        foreach ($reportTask->emails as $email) {
            $email->delete();
        }
        if ($request->has('email')) {
            foreach ($request->get('email') as $email) {
                $reportEmail = ReportEmail::create(array(
                    "report_task_id" => $reportTask->getKey(),
                    "report_email_address" => $email
                ));
                $reportEmails[] = $reportEmail;
            }
        }

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'reportTask', 'reportEmails']));
            } else {
                return compact(['status', 'reportTask', 'reportEmails']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    public function deleteProductReport(Request $request, $product_id)
    {
        $product = $this->productRepo->getProduct($product_id);

        if (!is_null($product->reportTask)) {
            event(new ReportTaskDeleting($product->reportTask));
            $this->reportTaskRepo->deleteReportTask($product->reportTask->getKey());
            event(new ReportTaskDeleted($product->reportTask));
        }
        $status = true;

        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }
}