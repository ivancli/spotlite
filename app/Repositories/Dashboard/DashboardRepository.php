<?php
namespace App\Repositories\Dashboard;

use App\Contracts\Repository\Dashboard\DashboardContract;
use App\Filters\QueryFilter;
use App\Models\Dashboard\Dashboard;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:12 AM
 */
class DashboardRepository implements DashboardContract
{
    protected $dashboard;
    protected $request;

    public function __construct(Dashboard $dashboard, Request $request)
    {
//        $this->dashboard = $dashboard;
        $this->dashboard = auth()->user()->dashboards();

        $this->request = $request;
    }

    public function getDashboards()
    {
        return auth()->user()->dashboards;
    }

    public function getDashboard($id, $fail = true)
    {
        if ($fail == true) {
            $dashboard = $this->dashboard->findOrFail($id);
            if ($dashboard->user->getKey() == auth()->user()->getKey()) {
                return $dashboard;
            }
        } else {
            $dashboard = $this->dashboard->find($id);
            if (!is_null($dashboard) && $dashboard->user->getKey() == auth()->user()->getKey()) {
                return $dashboard;
            }
        }
        return null;
    }

    public function storeDashboard($options)
    {
        return $this->dashboard->create($options);
    }

    public function updateDashboard($options, $id)
    {
        $dashboard = $this->getDashboard($id);
        $dashboard->update($options);
        return $dashboard;
    }

    public function deleteDashboard($id)
    {
        $dashboard = $this->getDashboard($id);
        $dashboard->delete();
        $this->cleanupDashboardOrder();
    }

    public function getDataTableDashboards(QueryFilter $queryFilter)
    {
        $dashboards = $this->dashboard->filter($queryFilter)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getDashboardCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $dashboards->count();
        } else {
            $output->recordsFiltered = $this->getDashboardCount();
        }
        $output->data = $dashboards->toArray();
        return $output;
    }

    public function getDashboardCount()
    {
        return $this->dashboard->count();
    }

    public function cleanupDashboardOrder()
    {
        $dashboards = $this->getDashboards();
        $dashboards->each(function ($dashboard, $index) {
            $dashboard->dashboard_order = $index + 1;
            $dashboard->save();
        });
    }
}