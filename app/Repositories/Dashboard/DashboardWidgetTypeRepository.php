<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 5:30 PM
 */

namespace App\Repositories\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardWidgetTypeContract;
use App\Models\Dashboard\DashboardWidgetType;
use Illuminate\Http\Request;

class DashboardWidgetTypeRepository implements DashboardWidgetTypeContract
{
    protected $request;
    protected $dashboardWidgetType;

    public function __construct(DashboardWidgetType $dashboardWidgetType, Request $request)
    {
        $this->request = $request;
        $this->dashboardWidgetType = $dashboardWidgetType;
    }


    public function getDashboardWidgetTypes()
    {
        return $this->dashboardWidgetType->all();
    }

    public function getDashboardWidgetType($id, $fail = true)
    {
        if ($fail === true) {
            return $this->dashboardWidgetType->findOrFail($id);
        } else {
            return $this->dashboardWidgetType->find($id);
        }
    }

    public function storeDashboardWidgetType($options)
    {
        $this->dashboardWidgetType->create($options);
    }

    public function updateDashboardWidgetType($options, $id)
    {
        $dashboardWidgetType = $this->getDashboardWidgetType($id);
        $dashboardWidgetType->update($options);
    }

    public function deleteDashboardWidgetType($id)
    {
        $dashboardWidgetType = $this->getDashboardWidgetType($id);
        $dashboardWidgetType->delete();
    }
}