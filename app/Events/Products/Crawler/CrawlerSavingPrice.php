<?php
namespace App\Events\Products\Crawler;

use App\Events\Event;
use App\Models\Crawler;
use Illuminate\Queue\SerializesModels;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 5:16 PM
 */
class CrawlerSavingPrice extends Event
{
    use SerializesModels;

    public $crawler;

    /**
     * Create a new event instance.
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
