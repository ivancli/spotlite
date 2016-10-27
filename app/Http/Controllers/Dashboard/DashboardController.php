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
use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Chargify;

class DashboardController extends Controller
{
    protected $dashboardRepo;
    protected $dashboardTemplateRepo;

    protected $queryFilter;
    protected $request;

    public function __construct(QueryFilter $queryFilter, Request $request,
                                DashboardContract $dashboardContract, DashboardTemplateContract $dashboardTemplateContract)
    {
        $this->dashboardRepo = $dashboardContract;
        $this->dashboardTemplateRepo = $dashboardTemplateContract;

        $this->queryFilter = $queryFilter;
        $this->request = $request;
    }

    /**
     * Go to Dashboard page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        $dashboard = auth()->user()->dashboards()->orderBy('dashboard_order', 'asc')->first();
        if (!is_null($dashboard)) {
            return view('dashboard.home')->with(compact(['dashboard']));
        }
        return redirect()->route("product.index");
    }

    /**
     * Go to Dashboard Management page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage()
    {
        if ($this->request->ajax()) {
            /*TODO load dashboards*/
            $dashboard = $this->dashboardRepo->getDataTableDashboards($this->queryFilter);
            if ($this->request->wantsJson()) {
                return response()->json($dashboard);
            } else {
                return $dashboard;
            }
        } else {
            return view('dashboard.manage');
        }
    }

    public function editFilter($dashboard_id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($dashboard_id);
        return view('dashboard.filter')->with(compact(['dashboard']));
    }

    public function updateFilter($dashboard_id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($dashboard_id);
        if ($dashboard->user_id != auth()->user()->getKey()) {
            abort(404);
            return false;
        }
        if ($this->request->has('timespan')) {
            $dashboard->setPreference('timespan', $this->request->get('timespan'));
        }

        if ($this->request->has('resolution')) {
            $dashboard->setPreference('resolution', $this->request->get('resolution'));
        }

        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['dashboard', 'status']));
            } else {
                return compact(['dashboard', 'status']);
            }
        } else {
            return redirect()->route('dashboard.show', $dashboard->getKey())->with(compact(['dashboard']));
        }
    }

    public function deleteFilter($dashboard_id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($dashboard_id);
        if ($dashboard->user_id != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        $dashboard->deletePreference('timespan');
        $dashboard->deletePreference('resolution');
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('dashboard.show', $dashboard)->with(compact(['dashboard']));
        }
    }

    public function show($id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($id);
        if ($dashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        return view('dashboard.home')->with(compact(['dashboard']));
    }

    public function create()
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

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {
                return view('dashboard.create')->with(compact(['templates', 'orders']));
            }
        } else {
            return view('dashboard.create')->with(compact(['templates', 'orders']));
        }
    }

    public function store(StoreValidator $storeValidator)
    {
        try {
            $storeValidator->validate($this->request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        $dashboard = $this->dashboardRepo->storeDashboard($this->request->all());
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
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['dashboard', 'status']));
            } else {
                return compact(['dashboard', 'status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }
    }

    public function edit($id)
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

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {
                return view('dashboard.edit')->with(compact(['templates', 'dashboard', 'orders']));
            }
        } else {
            return view('dashboard.edit')->with(compact(['templates', 'dashboard', 'orders']));
        }
    }

    public function update(UpdateValidator $updateValidator, $id)
    {
        $tempDashboard = $this->dashboardRepo->getDashboard($id);
        if ($tempDashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }

        try {
            $input = $this->request->all();
            $input['dashboard_id'] = $id;
            $updateValidator->validate($input);
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
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
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['dashboard', 'status']));
            } else {
                return compact(['dashboard', 'status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }
    }

    public function destroy($id)
    {
        $dashboard = $this->dashboardRepo->getDashboard($id);
        if ($dashboard->user->getKey() != auth()->user()->getKey()) {
            abort(404);
            return false;
        }
        $this->dashboardRepo->deleteDashboard($id);
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('dashboard.manage');
        }

    }
}