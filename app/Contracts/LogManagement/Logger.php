<?php
namespace App\Contracts\LogManagement;

use App\Models\User;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 11:10 AM
 */
interface Logger
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
     * @param User $user
     * @return mixed
     */
    public function storeLog($options, User $user = null);

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param User $user
     * @return mixed
     */
    public function updateLog($log_id, $options, User $user = null);

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id);
}