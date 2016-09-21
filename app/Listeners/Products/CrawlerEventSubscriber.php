<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 5:12 PM
 */

namespace App\Listeners\Products;


use App\Jobs\AlertUser;
use App\Jobs\LogCrawlerActivity;

class CrawlerEventSubscriber
{
    public function onCrawlerSavingPrice($event)
    {
        $crawler = $event->crawler;
        dispatch((new LogCrawlerActivity($crawler, array("type" => 'saving_price')))->onQueue("logging"));

    }

    public function onCrawlerRunning($event)
    {
        $crawler = $event->crawler;
        dispatch((new LogCrawlerActivity($crawler, array("type" => 'running')))->onQueue("logging"));
    }

    public function onCrawlerLoadingPrice($event)
    {
        $crawler = $event->crawler;
        dispatch((new LogCrawlerActivity($crawler, array("type" => 'loading_price')))->onQueue("logging"));
    }

    public function onCrawlerLoadingHTML($event)
    {
        $crawler = $event->crawler;
        dispatch((new LogCrawlerActivity($crawler, array("type" => 'loading_html')))->onQueue("logging"));
    }

    public function onCrawlerFinishing($event)
    {
        $crawler = $event->crawler;
        dispatch((new AlertUser($crawler))->onQueue("alerting"));
        dispatch((new LogCrawlerActivity($crawler, array("type" => 'finished')))->onQueue("logging"));
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Products\Crawler\CrawlerSavingPrice',
            'App\Listeners\Products\CrawlerEventSubscriber@onCrawlerSavingPrice'
        );
        $events->listen(
            'App\Events\Products\Crawler\CrawlerRunning',
            'App\Listeners\Products\CrawlerEventSubscriber@onCrawlerRunning'
        );
        $events->listen(
            'App\Events\Products\Crawler\CrawlerLoadingPrice',
            'App\Listeners\Products\CrawlerEventSubscriber@onCrawlerLoadingPrice'
        );
        $events->listen(
            'App\Events\Products\Crawler\CrawlerLoadingHTML',
            'App\Listeners\Products\CrawlerEventSubscriber@onCrawlerLoadingHTML'
        );
        $events->listen(
            'App\Events\Products\Crawler\CrawlerFinishing',
            'App\Listeners\Products\CrawlerEventSubscriber@onCrawlerFinishing'
        );
    }
}