<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 1:34 PM
 */

namespace App\Repositories\Logger;


use App\Contracts\Repository\Logger\ReportActivityLoggerContract;
use App\Models\Logs\ReportActivityLog;
use App\Models\Report;

class ReportActivityLoggerRepository implements ReportActivityLoggerContract
{

    /**
     * get all logs
     * @return mixed
     */
    public function getLogs()
    {
        // TODO: Implement getLogs() method.
    }

    /**
     * get a single log
     * @param $log_id
     * @param $haltOrFail
     * @return mixed
     */
    public function getLog($log_id, $haltOrFail = false)
    {
        // TODO: Implement getLog() method.
    }

    /**
     * create a log
     * @param $options
     * @return mixed
     */
    public function storeLog($options)
    {
        return ReportActivityLog::create($options);
    }

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @return mixed
     */
    public function updateLog($log_id, $options)
    {
        // TODO: Implement updateLog() method.
    }

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id)
    {
        // TODO: Implement deleteLog() method.
    }
}