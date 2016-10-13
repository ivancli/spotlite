<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardContract;
use App\Contracts\Repository\Dashboard\DashboardTemplateContract;
use App\Exceptions\ValidationException;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use App\Models\Dashboard\DashboardTemplate;
use App\Validators\Dashboard\Dashboard\StoreValidator;
use App\Validators\Dashboard\Dashboard\UpdateValidator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardRepo;
    protected $dashboardTemplateRepo;

    protected $queryFilter;

    public function __construct(QueryFilter $queryFilter,
                                DashboardContract $dashboardContract, DashboardTemplateContract $dashboardTemplateContract)
    {
        $this->dashboardRepo = $dashboardContract;
        $this->dashboardTemplateRepo = $dashboardTemplateContract;

        $this->queryFilter = $queryFilter;
    }

    /**
     * Go to Dashboard page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return redirect()->route("product.index");
    }

    /**
     * Go to Dashboard Management page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage(Request $request)
    {
        if ($request->ajax()) {
            /*TODO load dashboards*/
            $dashboard = $this->dashboardRepo->getDataTableDashboards($this->queryFilter);
            if ($request->wantsJson()) {
                return response()->json($dashboard);
            } else {
                return $dashboard;
            }
        } else {
            return view('dashboard.manage');
        }


    }

    public function show(Request $request, $id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($id);
        if ($dashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        return view('dashboard.home')->with(compact(['dashboard']));
    }

    public function create(Request $request)
    {
        $templates = $this->dashboardTemplateRepo->getTemplates();
        $templates = $templates->pluck("dashboard_template_name", (new DashboardTemplate())->getKeyName())->all();

        /*ordering*/
        $dashboards = $this->dashboardRepo->getDashboards();
        $dashboards = $dashboards->sortBy('dashboard_order');
        $dashboards = $dashboards->pluck('dashboard_name', 'dashboard_order');
        $orders = array();
        foreach ($dashboards as $order => $dashboard_name) {
            $dashboard_name = "After \"" . $dashboard_name . "\"";
            $orders[$order + 1] = $dashboard_name;
        }
        $orders = collect($orders);
        $orders->prepend('At the beginning', 1);

        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                return view('dashboard.create')->with(compact(['templates', 'orders']));
            }
        } else {
            return view('dashboard.create')->with(compact(['templates', 'orders']));
        }
    }

    public function store(StoreValidator $storeValidator, Request $request)
    {
        try {
            $storeValidator->validate($request->all());
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

        $dashboard = $this->dashboardRepo->storeDashboard($request->all());
        $dashboards = $this->dashboardRepo->getDashboards();
        $dashboards = $dashboards->reject(function ($tempDashboard, $index) use ($dashboard) {
            return $tempDashboard->getKey() == $dashboard->getKey();
        });

        $dashboards->splice($dashboard->dashboard_order - 1, 0, [$dashboard]);

        foreach ($dashboards as $ordering => $tempDashboard) {
            $tempDashboard->dashboard_order = $ordering + 1;
            $tempDashboard->save();
        }

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['dashboard', 'status']));
            } else {
                return compact(['dashboard', 'status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }
    }

    public function edit(Request $request, $id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($id);
        if ($dashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        $templates = $this->dashboardTemplateRepo->getTemplates();
        $templates = $templates->pluck("dashboard_template_name", (new DashboardTemplate())->getKeyName())->all();

        /*ordering*/
        $dashboards = $this->dashboardRepo->getDashboards();
        $dashboards = $dashboards->sortBy('dashboard_order');

        $dashboards = $dashboards->reject(function ($tempDashboard, $index) use ($dashboard) {
            return $tempDashboard->getKey() == $dashboard->getKey();
        });
        $dashboards = $dashboards->pluck('dashboard_name', 'dashboard_order');


        $orders = array();
        foreach ($dashboards as $order => $dashboard_name) {
            $dashboard_name = "After \"" . $dashboard_name . "\"";
            $orders[$order + 1] = $dashboard_name;
        }
        $orders = collect($orders);
        $orders->prepend('At the beginning', 1);

        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                return view('dashboard.edit')->with(compact(['templates', 'dashboard', 'orders']));
            }
        } else {
            return view('dashboard.edit')->with(compact(['templates', 'dashboard', 'orders']));
        }
    }

    public function update(UpdateValidator $updateValidator, Request $request, $id)
    {
        $tempDashboard = $this->dashboardRepo->getDashboard($id);
        if ($tempDashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        try {
            $input = $request->all();
            $input['dashboard_id'] = $id;
            $updateValidator->validate($input);
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

        if (!isset($input['is_hidden'])) {
            $input['is_hidden'] = null;
        }

        $dashboard = $this->dashboardRepo->updateDashboard($input, $id);
        $dashboards = $this->dashboardRepo->getDashboards();
        $dashboards = $dashboards->reject(function ($tempDashboard, $index) use ($dashboard) {
            return $tempDashboard->getKey() == $dashboard->getKey();
        });

        $dashboards->splice($dashboard->dashboard_order - 1, 0, [$dashboard]);

        foreach ($dashboards as $ordering => $tempDashboard) {
            $tempDashboard->dashboard_order = $ordering + 1;
            $tempDashboard->save();
        }




        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['dashboard', 'status']));
            } else {
                return compact(['dashboard', 'status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }
    }

    public function destroy(Request $request, $id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($id);
        if ($dashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }
        $this->dashboardRepo->deleteDashboard($id);
        $status = true;

        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }

    }
}