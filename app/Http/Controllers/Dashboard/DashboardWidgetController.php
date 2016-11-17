<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 4:39 PM
 */

namespace App\Http\Controllers\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardContract;
use App\Contracts\Repository\Dashboard\DashboardWidgetContract;
use App\Contracts\Repository\Dashboard\DashboardWidgetTypeContract;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Validators\Dashboard\DashboardWidget\StoreValidator;
use App\Validators\Dashboard\DashboardWidget\UpdateValidator;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    protected $request;

    protected $dashboardRepo;
    protected $dashboardWidgetRepo;
    protected $dashboardWidgetTypeRepo;


    public function __construct(Request $request, DashboardContract $dashboardContract, DashboardWidgetContract $dashboardWidgetContract, DashboardWidgetTypeContract $dashboardWidgetTypeContract)
    {
        $this->middleware('permission:create_dashboard_widget', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_dashboard_widget', ['only' => ['show']]);
        $this->middleware('permission:update_dashboard_widget', ['only' => ['edit', 'update', 'updateOrder']]);
        $this->middleware('permission:delete_dashboard_widget', ['only' => ['delete']]);


        $this->request = $request;
        $this->dashboardRepo = $dashboardContract;
        $this->dashboardWidgetRepo = $dashboardWidgetContract;
        $this->dashboardWidgetTypeRepo = $dashboardWidgetTypeContract;
    }

    public function create()
    {
        $dashboard = $this->dashboardRepo->getDashboard($this->request->get('dashboard_id'));

        $widgetTypes = $this->dashboardWidgetTypeRepo->getDashboardWidgetTypes();
        $widgetTypes = $widgetTypes->pluck('dashboard_widget_type_name', 'dashboard_widget_type_id')->all();

        $categories = auth()->user()->categories()->with('products.sites')->get();

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {
                return view('dashboard.widget.create')->with(compact(['widgetTypes', 'categories', 'dashboard']));
            }
        } else {
            return view('dashboard.widget.create')->with(compact(['widgetTypes', 'categories', 'dashboard']));
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
        if($this->request->get('timespan') == 'custom'){
            $status = false;
            $errors = array("Cannot add content with custom timespan, please choose different timespan to add to dashboard.");
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

        $dashboardWidget = $this->dashboardWidgetRepo->storeWidget($this->request->all());

        //widget type is chart
        if ($dashboardWidget->dashboard_widget_type_id == 1) {
            $dashboardWidget->setPreference("chart_type", $this->request->get('chart_type'));
            switch ($this->request->get('chart_type')) {
                case "site":
                    $dashboardWidget->setPreference("site_id", $this->request->get('site_id'));
                case "product":
                    $dashboardWidget->setPreference("product_id", $this->request->get('product_id'));
                case "category":
                    $dashboardWidget->setPreference("category_id", $this->request->get('category_id'));
                    break;
                default:
            }
            $dashboardWidget->setPreference("timespan", $this->request->get('timespan'));
            $dashboardWidget->setPreference("resolution", $this->request->get('resolution'));
        }
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'dashboardWidget']));
            } else {
                return compact(['status', 'dashboardWidget']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    public function edit($id)
    {
        $widget = $this->dashboardWidgetRepo->getWidget($id);

        $widgetTypes = $this->dashboardWidgetTypeRepo->getDashboardWidgetTypes();
        $widgetTypes = $widgetTypes->pluck('dashboard_widget_type_name', 'dashboard_widget_type_id')->all();

        $categories = auth()->user()->categories()->with('products.sites')->get();

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {
                return view('dashboard.widget.edit')->with(compact(['widgetTypes', 'categories', 'widget']));
            }
        } else {
            return view('dashboard.widget.edit')->with(compact(['widgetTypes', 'categories', 'widget']));
        }
    }

    public function show($id)
    {
        $widget = $this->dashboardWidgetRepo->getWidget($id);
        if (is_null($widget->dashboard) || $widget->dashboard->user_id != auth()->user()->getKey()) {
            abort(404);
            return false;
        }
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                /*TODO load chart data here*/
                $data = $this->dashboardWidgetRepo->getWidgetData($id);
                $status = true;
                return response()->json(compact(['status', 'data']));
            } else {
                return view('dashboard.widget.templates.' . $widget->widgetType->template->dashboard_widget_template_name)->with(compact(['widget']));
            }
        } else {
            return view('dashboard.widget.templates.' . $widget->widgetType->template->dashboard_widget_template_name)->with(compact(['widget']));
        }
    }

    public function update(UpdateValidator $updateValidator, $id)
    {
        try {
            $updateValidator->validate($this->request->all());
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
        $dashboardWidget = $this->dashboardWidgetRepo->updateWidget($this->request->all(), $id);

        $dashboardWidget->clearPreferences();

        //widget type is chart
        if ($dashboardWidget->dashboard_widget_type_id == 1) {
            $dashboardWidget->setPreference("chart_type", $this->request->get('chart_type'));
            switch ($this->request->get('chart_type')) {
                case "site":
                    $dashboardWidget->setPreference("site_id", $this->request->get('site_id'));
                case "product":
                    $dashboardWidget->setPreference("product_id", $this->request->get('product_id'));
                case "category":
                    $dashboardWidget->setPreference("category_id", $this->request->get('category_id'));
                    break;
                default:
            }
            $dashboardWidget->setPreference("timespan", $this->request->get('timespan'));
            $dashboardWidget->setPreference("resolution", $this->request->get('resolution'));
        }
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'dashboardWidget']));
            } else {
                return compact(['status', 'dashboardWidget']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    public function updateOrder()
    {
        $order = $this->request->get('widget_order');

        foreach ($order as $key => $ord) {
            $widget = $this->dashboardWidgetRepo->getWidget($ord['dashboard_widget_id'], false);
            if (!is_null($widget) && intval($ord['dashboard_widget_order']) != 0) {
                $widget->dashboard_widget_order = intval($ord['dashboard_widget_order']);
                $widget->save();
            }
        }
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {

        }
    }

    public function destroy($id)
    {
        $dashboardWidget = $this->dashboardWidgetRepo->getWidget($id);
        if (is_null($dashboardWidget->dashboard) || $dashboardWidget->dashboard->user_id != auth()->user()->getKey()) {
            abort(404);
            return false;
        }
        $this->dashboardWidgetRepo->deleteWidget($id);
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('dashboard.show', $dashboardWidget->dashboard->getKey());
        }
    }
}