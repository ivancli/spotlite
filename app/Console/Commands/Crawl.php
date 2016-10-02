<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2016
 * Time: 3:44 PM
 */

namespace App\Console\Commands;


use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Jobs\CrawlSite;
use App\Models\AppPreference;
use Illuminate\Console\Command;

class Crawl extends Command
{
    protected $signature = "crawl:run";
    protected $description = 'Pushing available crawlers to queue';

    protected $crawler = null;

    public function __construct(CrawlerContract $crawlerContract)
    {
        parent::__construct();
        $this->crawler = $crawlerContract;
    }

    public function handle()
    {
        $lastReservedAt = AppPreference::getCrawlLastReservedAt();
        $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
        $currentRoundedHours = date("Y-m-d H:00:00");
        if (AppPreference::getCrawlReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
            /*reserve the task*/
            AppPreference::setCrawlReserved();
            AppPreference::setCrawlLastReservedAt();

            /* get the designed crawl time */
            $crawlTimes = AppPreference::getCrawlTimes();
            $currentHour = intval(date("H"));

            /* in the designed crawl time? */
            if (in_array($currentHour, $crawlTimes)) {
                $crawlers = $this->crawler->getCrawlers();

                foreach ($crawlers as $crawler) {
                    if (is_null($crawler->last_active_at) || intval((time() - strtotime($crawler->last_active_at)) / 3600) != 0) {
                        dispatch((new CrawlSite($crawler))->onQueue("crawling"));
                        $crawler->queue();
                    } else {

                    }
                }
            }
            AppPreference::setCrawlReserved('n');
        }
    }
}