<?php
namespace App\Contracts\Repository\Dashboard;

use App\Filters\QueryFilter;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 10:11 AM
 */
interface DashboardContract
{
    public function getDashboards();

    public function getDashboard($id, $fail = true);

    public function storeDashboard($options);

    public function updateDashboard($options, $id);

    public function deleteDashboard($id);

    public function getDataTableDashboards(QueryFilter $queryFilter);

    public function getDashboardCount();

    public function cleanupDashboardOrder();
}