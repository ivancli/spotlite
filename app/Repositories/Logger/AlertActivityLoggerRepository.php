<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 10:32 AM
 */

namespace App\Repositories\Logger;


use App\Contracts\Repository\Logger\AlertActivityLoggerContract;
use App\Filters\QueryFilter;
use App\Models\Alert;
use App\Models\Logs\AlertActivityLog;
use Illuminate\Http\Request;

class AlertActivityLoggerRepository implements AlertActivityLoggerContract
{
    protected $alertActivityLog;
    protected $request;

    public function __construct(AlertActivityLog $alertActivityLog, Request $request)
    {
        $this->alertActivityLog = $alertActivityLog;
        $this->request = $request;
    }

    /**
     * get all logs
     * @return mixed
     */
    public function getLogs()
    {
        return AlertActivityLog::all();
    }

    /**
     * get all logs in DataTables format
     * @param QueryFilter $filters
     * @return mixed
     */
    public function getDataTablesLogs(QueryFilter $filters)
    {
        // TODO: Implement getDataTablesLogs() method.
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
     * @param Alert $alert
     * @return mixed
     */
    public function storeLog($options, Alert $alert = null)
    {
        $fields = array(
            "alert_id" => $alert->getKey(),
            "alert_activity_log_owner_type" => $alert->alert_owner_type,
            "alert_activity_log_owner_id" => $alert->alert_owner_id,
            "type" => $options['type'],
            "content" => json_encode($options),
        );
        $log = $this->alertActivityLog->create($fields);
        return $log;
    }

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param Alert $alert
     * @return mixed
     */
    public function updateLog($log_id, $options, Alert $alert = null)
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

    public function getDataTableAlertActivityLogs()
    {
        $categoryAlertLogs = $this->getCategoryAlertLogsByAuthUser();
        $productAlertLogs = $this->getProductAlertLogsByAuthUser();
        $siteAlertLogs = $this->getSiteAlertLogsByAuthUser();

        $alertLogs = $productAlertLogs->merge($categoryAlertLogs, $siteAlertLogs);


        $alertLogCount = $alertLogs->count();

        $alertLogs = $alertLogs->sortByDesc('created_at');

        if ($this->request->has('start')) {
            $alertLogs = $alertLogs->slice($this->request->get('start'), $alertLogs->count());
        }

        if ($this->request->has('length')) {
            $alertLogs = $alertLogs->take($this->request->get('length'));
        }
        $alertLogs = $alertLogs->values();

        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $alertLogCount;
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $alertLogs->count();
        } else {
            $output->recordsFiltered = $alertLogCount;
        }
        $output->data = $alertLogs->toArray();
        return $output;

    }

    public function getCategoryAlertLogsByAuthUser()
    {
        $categoryLogs = auth()->user()->categories()->with('alertActivityLogs.alertActivityLoggable')->get()->pluck('alertActivityLogs')->flatten();
        $categoryLogs = $categoryLogs->reject(function ($categoryLog, $key) {
            return $categoryLog->type != 'sent';
        });
        return $categoryLogs;
    }

    public function getProductAlertLogsByAuthUser()
    {
        $productLogs = auth()->user()->products()->with('alertActivityLogs.alertActivityLoggable')->get()->pluck('alertActivityLogs')->flatten();
        $productLogs = $productLogs->reject(function ($productLog, $key) {
            return $productLog->type != 'sent';
        });
        return $productLogs;
    }

    public function getSiteAlertLogsByAuthUser()
    {
        $siteLogs = auth()->user()->sites()->with('alertActivityLogs.alertActivityLoggable')->get()->pluck('alertActivityLogs')->flatten();
        $siteLogs = $siteLogs->reject(function ($siteLog, $key) {
            return $siteLog->type != 'sent';
        });
        return $siteLogs;
    }
}