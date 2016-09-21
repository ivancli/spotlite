<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 1:49 PM
 */

namespace App\Repositories\LogManagement;


use App\Contracts\LogManagement\CrawlerLogger;
use App\Filters\QueryFilter;
use App\Models\Crawler;
use App\Models\Logs\CrawlerLog;

class SLCrawlerLogger implements CrawlerLogger
{
    protected $crawlerLog;

    public function __construct(CrawlerLog $crawlerLog)
    {
        $this->crawlerLog = $crawlerLog;
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
     * @param Crawler $crawler
     * @return mixed
     */
    public function storeLog($options, Crawler $crawler = null)
    {
        $content = array(
            "crawler_id" => $crawler->getKey(),
            "url" => $crawler->site->site_url,
            "xpath" => $crawler->site->xpath
        );

        $fields = array(
            "crawler_id" => $crawler->getKey(),
            "type" => $options['type'],
            "content" => json_encode($content),
        );
        $log = $this->crawlerLog->create($fields);
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
            "content" => $crawler->toJson(),
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