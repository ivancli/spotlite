<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 2:05 PM
 */

namespace App\Jobs;


use App\Contracts\LogManagement\CrawlerActivityLogger;
use App\Models\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogCrawlerActivity extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $options;
    protected $crawler;

    /**
     * Create a new job instance.
     *
     * @param Crawler $crawler
     * @param $options
     */
    public function __construct(Crawler $crawler, $options)
    {
        $this->options = $options;
        $this->crawler = $crawler;
    }

    /**
     * Execute the job.
     * @param CrawlerActivityLogger $crawlerActivityLogger
     */
    public function handle(CrawlerActivityLogger $crawlerActivityLogger)
    {
        $crawlerActivityLogger->storeLog($this->options, $this->crawler);
    }
}