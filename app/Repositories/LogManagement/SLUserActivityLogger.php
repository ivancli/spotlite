<?php
namespace App\Repositories\LogManagement;

use App\Contracts\LogManagement\UserActivityLogger;
use App\Filters\QueryFilter;
use App\Models\Logs\UserActivityLog;
use App\Models\User;
use Illuminate\Http\Request;


/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/1/2016
 * Time: 11:28 AM
 */
class SLUserActivityLogger implements UserActivityLogger
{
    protected $userActivityLog;
    protected $request;

    public function __construct(UserActivityLog $userActivityLog, Request $request)
    {
        $this->userActivityLog = $userActivityLog;
        $this->request = $request;
    }

    /**
     * get all logs
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getLogs()
    {
        return $this->userActivityLog->all();
    }

    /**
     * get all logs and filter in DataTables format
     * @param QueryFilter $filters
     * @return \stdClass
     */
    public function getDataTablesLogs(QueryFilter $filters)
    {
        $logs = $this->userActivityLog->with('owner')->filter($filters)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getLogCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $logs->count();
        } else {
            $output->recordsFiltered = $this->getLogCount();
        }
        $output->data = $logs->toArray();
        return $output;
    }

    /**
     * get all logs of a user in DataTables format
     * @param QueryFilter $filters
     * @param User $user
     * @return mixed
     */
    public function getDataTablesLogsByUser(QueryFilter $filters, User $user)
    {
        $logs = $this->userActivityLog->with('owner')->filter($filters)->where('user_id', $user->getKey())->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getLogCount($user->getKey());
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $logs->count();
        } else {
            $output->recordsFiltered = $this->getLogCount($user->getKey());
        }
        $output->data = $logs->toArray();
        return $output;
    }

    /**
     * get total number of logs by itself of user_id
     * @param null $user_id
     * @return mixed
     */
    public function getLogCount($user_id = null)
    {
        if (is_null($user_id)) {
            return $this->userActivityLog->count();
        } else {
            return $this->userActivityLog->where("user_id", $user_id)->count();
        }
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