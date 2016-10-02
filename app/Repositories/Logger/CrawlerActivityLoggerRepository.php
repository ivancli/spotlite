<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 1:49 PM
 */

namespace App\Repositories\Logger;


use App\Contracts\Repository\Logger\CrawlerActivityLoggerContract;
use App\Filters\QueryFilter;
use App\Models\Crawler;
use App\Models\Logs\CrawlerActivityLog;
use Illuminate\Http\Request;

class CrawlerActivityLoggerRepository implements CrawlerActivityLoggerContract
{
    protected $crawlerActivityLog;
    protected $request;

    public function __construct(CrawlerActivityLog $crawlerActivityLog, Request $request)
    {
        $this->crawlerActivityLog = $crawlerActivityLog;
        $this->request = $request;
    }

    /**
     * get all logs
     * @return mixed
     */
    public function getLogs()
    {
        // TODO: Implement getLogs() method.
    }

    /**
     * get all logs in DataTables format
     * @param QueryFilter $filters
     * @return mixed
     */
    public function getDataTablesLogs(QueryFilter $filters)
    {
        $logs = $this->crawlerActivityLog->filter($filters)->get();
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

    public function getLogCount()
    {
        return $this->crawlerActivityLog->count();
    }

    /**
     * get a single log
     * @param $log_id
     * @param $haltOrFail
     * @return mixed
     */
    public function getLog($log_id, $haltOrFail = false)
    {
        return $haltOrFail ? $this->crawlerActivityLog->findOrFail($log_id) : $this->crawlerActivityLog->find($log_id);
    }

    /**
     * create a log
     * @param $options
     * @param Crawler $crawler
     * @return mixed
     */
    public function storeLog($options, Crawler $crawler = null)
    {
        $message = array(
            "crawler_id" => $crawler->getKey(),
            "url" => $crawler->site->site_url,
            "xpath" => $crawler->site->site_xpath
        );

        $fields = array(
            "crawler_id" => $crawler->getKey(),
            "status" => $options['status'],
            "message" => json_encode($message),
        );
        $log = $this->crawlerActivityLog->create($fields);
        return $log;
    }

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param Crawler $crawler
     * @return mixed
     */
    public function updateLog($log_id, $options, Crawler $crawler = null)
    {
        $log = $this->getLog($log_id);
        $fields = array(
            "crawler_id" => $crawler->getKey(),
            "type" => $options['type'],
            "message" => $crawler->toJson(),
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
        $log = $this->getLog($log_id);
        $log->delete();
        return true;
    }
}