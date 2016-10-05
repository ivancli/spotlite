<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 1:30 PM
 */

namespace App\Contracts\Repository\Logger;



interface ReportActivityLoggerContract
{
    /**
     * get all logs
     * @return mixed
     */
    public function getLogs();

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
     * @return mixed
     */
    public function storeLog($options);

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @return mixed
     */
    public function updateLog($log_id, $options);

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id);
}