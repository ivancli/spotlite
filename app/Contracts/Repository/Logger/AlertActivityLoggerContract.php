<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 10:30 AM
 */

namespace App\Contracts\Repository\Logger;


use App\Filters\QueryFilter;
use App\Models\Alert;

interface AlertActivityLoggerContract
{
    /**
     * get all logs
     * @return mixed
     */
    public function getLogs();

    /**
     * Load logs which belong to logged in user
     *
     * @return mixed
     */
    public function getLogsByAuthUser();

    /**
     * get all logs in DataTables format
     * @param QueryFilter $filters
     * @return mixed
     */
    public function getDataTablesLogs(QueryFilter $filters);

    /**
     * get a single log
     * @param $log_id
     * @param $haltOrFail
     * @return mixed
     */
    public function getLog($log_id, $haltOrFail = false);

    /**
     * create a log
     * @param $options
     * @param Alert $alert
     * @return mixed
     */
    public function storeLog($options, Alert $alert = null);

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param Alert $alert
     * @return mixed
     */
    public function updateLog($log_id, $options, Alert $alert = null);

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id);

    public function getProductAlertLogsByAuthUser();

    public function getProductSiteAlertLogsByAuthUser();

    public function getDataTableAlertActivityLogs();
}