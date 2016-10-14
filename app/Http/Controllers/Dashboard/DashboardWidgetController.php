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
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    protected $request;

    protected $dashboardRepo;
    protected $dashboardWidgetRepo;
    protected $dashboardWidgetTypeRepo;


    public function __construct(Request $request,
                                DashboardContract $dashboardContract, DashboardWidgetContract $dashboardWidgetContract, DashboardWidgetTypeContract $dashboardWidgetTypeContract)
    {
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
        $dashboardWidget = $this->dashboardWidgetRepo->storeWidget($this->request->all());

        //widget type is chart
        if ($dashboardWidget->dashboard_widget_type_id == 1) {
            $dashboardWidget->setPreference("chart_type", $this->request->get('chart_type'));
            switch ($this->request->get('chart_type')) {
                case "site":
                    $dashboardWidget->setPreference("site_id", $this->request->get('site_id'));
                    break;
                case "product":
                    $dashboardWidget->setPreference("product_id", $this->request->get('product_id'));
                    break;
                case "category":
                    $dashboardWidget->setPreference("product_id", $this->request->get('category_id'));
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

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {

            }
        } else {

        }
        return view('dashboard.widget.edit')->with(compact(['widget']));
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

    public function update($id)
    {

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