<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 11:13 AM
 */

namespace App\Jobs;


use App\Contracts\Repository\Crawler\CrawlerContract;
use App\Contracts\Repository\Ebay\EbayContract;
use App\Models\Crawler;
use App\Models\Domain;
use App\Models\EbayItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Invigor\Crawler\Contracts\CrawlerInterface;
use Invigor\Crawler\Contracts\ParserInterface;

class CrawlSite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $crawler;

    /**
     * Create a new job instance.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Execute the job.
     * @param CrawlerContract $crawler
     * @param EbayContract $ebayRepo
     * @return bool
     */
    public function handle(CrawlerContract $crawler, EbayContract $ebayRepo)
    {
        $this->crawler->pick();
        if (isset($this->crawler->site) && isset($this->crawler->site->product) && isset($this->crawler->site->product->user)) {
            $user = $this->crawler->site->product->user;
            /*check user subscription status*/
            if ($user->needSubscription && (is_null($this->crawler->site->product->user->subscription) || !$this->crawler->site->product->user->subscription->isValid())) {
                $this->crawler->resetStatus();
                return false;
            }
        }

        $crawler_class = "DefaultCrawler";
        $parser_class = "XPathParser";
        if (!is_null($this->crawler->crawler_class)) {
            $crawler_class = $this->crawler->crawler_class;
        } else {
            /*TODO enable domain in the second phase*/
            $domain = Domain::where("domain_url", $this->crawler->site->domain)->first();
            if (!is_null($domain) && !is_null($domain->crawler_class)) {
                $crawler_class = $domain->crawler_class;
            }
        }

        $crawlerClassFullPath = 'Invigor\Crawler\Repositories\Crawlers\\' . $crawler_class;

        if (!is_null($this->crawler->parser_class)) {
            $parser_class = $this->crawler->parser_class;
        } else {
            /*TODO enable domain in the second phase*/
            $domain = Domain::where("domain_url", $this->crawler->site->domain)->first();
            if (!is_null($domain) && !is_null($domain->parser_class)) {
                $parser_class = $domain->parser_class;
            }
        }
        $parserClassFullPath = 'Invigor\Crawler\Repositories\Parsers\\' . $parser_class;

        $currency_formatter_class = null;
        if (!is_null($this->crawler->currency_formatter_class)) {
            $currency_formatter_class = $this->crawler->currency_formatter_class;
        } else {
            /*TODO enable domain in the second phase*/
            $domain = Domain::where("domain_url", $this->crawler->site->domain)->first();
            if (!is_null($domain) && !is_null($domain->currency_formatter_class)) {
                $currency_formatter_class = $domain->currency_formatter_class;
            }
        }

        $crawlerClass = app()->make($crawlerClassFullPath);
        $parserClass = app()->make($parserClassFullPath);

        $currencyFormatterClass = null;
        if (!is_null($currency_formatter_class)) {
            $currencyFormatterClassFullPath = 'Invigor\Crawler\Repositories\CurrencyFormatters\\' . $currency_formatter_class;
            $currencyFormatterClass = app()->make($currencyFormatterClassFullPath);
        }

        $crawler->crawl($this->crawler, $crawlerClass, $parserClass, $currencyFormatterClass);

        /*TODO refine this part*/
        $site = $this->crawler->site;
        if (strpos($site->domain, 'www.ebay.com') !== false) {
            $url = $site->site_url;
            $path = parse_url($url)['path'];
            $tokens = explode('/', $path);
            $itemId = $tokens[count($tokens) - 1];
            if ($itemId) {
                $item = $ebayRepo->getItem($itemId);
                $ebayItem = $site->ebayItem;
                if (is_null($ebayItem)) {
                    $ebayItem = $site->ebayItem()->save(new EbayItem());
                }

                $ebayItem->title = isset($item->title) ? $item->title : null;
                $ebayItem->subtitle = isset($item->subtitle) ? $item->subtitle : null;
                $ebayItem->shortDescription = isset($item->shortDescription) ? $item->shortDescription : null;
                $ebayItem->price = isset($item->price) && isset($item->price->value) ? $item->price->value : null;
                $ebayItem->currency = isset($item->price) && isset($item->price->currency) ? $item->price->currency : null;
                $ebayItem->category = isset($item->categoryPath) ? $item->categoryPath : null;
                $ebayItem->condition = isset($item->condition) ? $item->condition : null;
                $ebayItem->location_city = isset($item->itemLocation) && isset($item->itemLocation->city) ? $item->itemLocation->city : null;
                $ebayItem->location_postcode = isset($item->itemLocation) && isset($item->itemLocation->postalCode) ? $item->itemLocation->postalCode : null;
                $ebayItem->location_country = isset($item->itemLocation) && isset($item->itemLocation->country) ? $item->itemLocation->country : null;
                $ebayItem->image_url = isset($item->image) && isset($item->image->imageUrl) ? $item->image->imageUrl : null;
                $ebayItem->brand = isset($item->brand) ? $item->brand : null;
                $ebayItem->seller_username = isset($item->seller) && isset($item->seller->username) ? $item->seller->username : null;

                $ebayItem->save();
            }
        }

        /* unset everything to prevent memory leak */
        unset($user, $crawler_class, $parser_class);
        if (isset($domain)) {
            unset($domain);
        }
        unset($crawlerClassFullPath, $parserClassFullPath, $crawlerClass, $parserClass, $currency_formatter_class, $currencyFormatterClass);
        unset($crawler);
    }
}