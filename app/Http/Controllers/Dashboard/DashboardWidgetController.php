<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 4:39 PM
 */

namespace App\Http\Controllers\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardWidgetContract;
use App\Contracts\Repository\Dashboard\DashboardWidgetTypeContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    protected $request;

    protected $dashboardWidgetRepo;
    protected $dashboardWidgetTypeRepo;

    public function __construct(Request $request, DashboardWidgetContract $dashboardWidgetContract, DashboardWidgetTypeContract $dashboardWidgetTypeContract)
    {
        $this->request = $request;
        $this->dashboardWidgetRepo = $dashboardWidgetContract;
        $this->dashboardWidgetTypeRepo = $dashboardWidgetTypeContract;
    }

    public function create()
    {

        $widgetTypes = $this->dashboardWidgetTypeRepo->getDashboardWidgetTypes();
        $widgetTypes = $widgetTypes->pluck('dashboard_widget_type_name', 'dashboard_widget_type_id')->all();

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {

            } else {
                return view('dashboard.widget.create')->with(compact(['widgetTypes']));
            }
        } else {
            return view('dashboard.widget.create')->with(compact(['widgetTypes']));
        }
    }

    public function store()
    {

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

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }
}