<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 1:47 PM
 */

namespace App\Contracts\LogManagement;


use App\Filters\QueryFilter;
use App\Models\Crawler;

interface CrawlerLogger
{
    /**
     * get all logs
     * @return mixed
     */
    public function getLogs();

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
     * @param Crawler $crawler
     * @return mixed
     */
    public function storeLog($options, Crawler $crawler = null);

    /**
     * update a log
     * @param $log_id
     * @param $options
     * @param Crawler $crawler
     * @return mixed
     */
    public function updateLog($log_id, $options, Crawler $crawler = null);

    /**
     * delete a log
     * @param $log_id
     * @return mixed
     */
    public function deleteLog($log_id);
}