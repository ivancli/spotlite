<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 11:13 AM
 */

namespace App\Jobs;


use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Models\Crawler;
use App\Models\Domain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

class CrawlSite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $crawler;
    protected $crawler_class;
    protected $parser_class;

    /**
     * Create a new job instance.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
        $crawler_class = "DefaultCrawler";
        $parser_class = "XPathParser";
        if (!is_null($this->crawler->crawler_class)) {
            $crawler_class = $this->crawler->crawler_class;
        } else {
            /*TODO enable domain in the second phase*/
//            $domain = Domain::where("domain_url", $this->crawler->site->domain)->first();
//            if (!is_null($domain) && !is_null($domain->crawler_class)) {
//                $crawler_class = $domain->crawler_class;
//            }
        }

        /* Unable to bind initiate the real instance instead */
//        App::bind('Invigor\Crawler\Contracts\CrawlerInterface', 'Invigor\Crawler\Repositories\Crawlers\\' . $crawler_class);
        $this->crawler_class = 'Invigor\Crawler\Repositories\Crawlers\\' . $crawler_class;


        if (!is_null($this->crawler->parser_class)) {
            $parser_class = $this->crawler->parser_class;
        } else {
            /*TODO enable domain in the second phase*/
//            $domain = Domain::where("domain_url", $this->crawler->site->domain)->first();
//            if (!is_null($domain) && !is_null($domain->parser_class)) {
//                $parser_class = $domain->parser_class;
//            }
        }

        /* Unable to bind initiate the real instance instead */
//        App::bind('Invigor\Crawler\Contracts\ParserInterface', 'Invigor\Crawler\Repositories\Parsers\\' . $parser_class);
        $this->parser_class = 'Invigor\Crawler\Repositories\Parsers\\' . $parser_class;
    }

    /**
     * Execute the job.
     * @param CrawlerContract $crawler
     */
//    public function handle(CrawlerManager $crawlerManager, CrawlerInterface $crawler, ParserInterface $parser)
    public function handle(CrawlerContract $crawler)
    {
        $crawlerClass = app()->make($this->crawler_class);
        $parserClass = app()->make($this->parser_class);
        $crawler->crawl($this->crawler, $crawlerClass, $parserClass);
    }
}