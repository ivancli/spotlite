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
use App\Jobs\SendMail;
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
                    /*check user subscription plan*/
                    if (isset($crawler->site) && isset($crawler->site->product) && isset($crawler->site->product->user)) {
                        $user = $crawler->site->product->user;
                        request()->session()->put('subscription_location', $user->subscription_location);
                        if ($user->needSubscription && !is_null($crawler->last_active_at)) {
                            $lastActiveAt = date('Y-m-d H:00:00', strtotime($crawler->last_active_at));
                            $hoursDifference = intval((strtotime($currentRoundedHours) - strtotime($lastActiveAt)) / 3600);
                            if (is_null($user->subscription) || !is_null($user->subscription->cancelled_at) || is_null($user->subscriptionCriteria()) || $user->subscriptionCriteria()->frequency > $hoursDifference) {
                                //not the time to crawl yet
                                continue;
                            }
                        }
                        dispatch((new CrawlSite($crawler))->onQueue("crawling"));
                        $crawler->queue();
                        $crawler->last_active_at = date("Y-m-d H:i:s");
                        $crawler->save();
                    }
                }
            }
            AppPreference::setCrawlReserved('n');
        } else {
            if (AppPreference::getCrawlReserved() == 'y' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
//                dispatch((new SendMail('errors.email.crawler', array(), array(
//                    "email" => config('error_notifier.email'),
//                    "subject" => 'Crawler Issue on SpotLite',
//                )))->onQueue("mailing"));
            }
        }
    }
}