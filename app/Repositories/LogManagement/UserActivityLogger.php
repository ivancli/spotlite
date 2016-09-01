<?php
namespace App\Repositories\LogManagement;

use App\Contracts\LogManagement\Logger;
use App\Models\Logs\UserActivityLog;
use App\Models\User;


/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 11:28 AM
 */
class UserActivityLogger implements Logger
{
    protected $userActivityLog;

    public function __construct(UserActivityLog $userActivityLog)
    {
        $this->userActivityLog = $userActivityLog;
    }

    /**
     * get all logs
     * @return mixed
     */
    public function getLogs()
    {
        $this->userActivityLog->all();
    }

    /**
     * get a single log
     * @param $log_id
     * @param $haltOrFail
     * @return mixed
     */
    public function getLog($log_id, $haltOrFail = false)
    {
        return $haltOrFail ? $this->userActivityLog->findOrFail($log_id) : $this->userActivityLog->find($log_id);
    }

    /**
     * create a log
     * @param $options
     * @param User $user
     * @return mixed
     */
    public function storeLog($options, User $user = null)
    {
        if (is_null($user)) {
            $user = auth()->user();
        }
        if (is_null($user)) {
            /*TODO handle user not found exception*/
            return false;
        }
        $fields = array(
            "user_id" => $user->getKey(),
            "activity" => $options,
        );
        $log = $this->userActivityLog->create($fields);
        return $log;
    }

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param User $user
     * @return mixed
     */
    public function updateLog($log_id, $options, User $user = null)
    {
        $log = $this->getLog($log_id, true);
        if (is_null($user)) {
            $user = auth()->user();
        }
        if (is_null($user)) {
            /*TODO handle user not found exception*/
            return false;
        }
        $fields = array(
            "user_id" => $user->getKey(),
            "activity" => $options,
        );
        $log->update($fields);
        return $log;
    }

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id)
    {
        $log = $this->getLog($log_id, true);
        $log->delete();
    }
}