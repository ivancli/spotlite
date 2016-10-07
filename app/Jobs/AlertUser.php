<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 5:34 PM
 */

namespace App\Jobs;


use App\Contracts\Repository\Product\Alert\AlertContract;
use App\Models\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlertUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $crawler;

    /**
     * Create a new job instance.
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Execute the job.
     * @param AlertContract $alertRepo
     */
    public function handle(AlertContract $alertRepo)
    {
        $site = $this->crawler->site;

        $productSites = $site->productSites;
        foreach ($productSites as $productSite) {
            if (!is_null($productSite->alert) && !$productSite->alert->lastActiveWithinHour()) {
                $productSite->alert->last_active_at = date("Y-m-d H:i:s");
                $productSite->alert->save();
                switch ($productSite->alert->alert_owner_type) {
                    case "product_site":
                        $alertRepo->triggerProductSiteAlert($productSite->alert);
                        break;
                    default:
                }
            }
        }


        $products = $site->products;
        foreach ($products as $product) {
            if (is_null($product->alert)) {
                continue;
            }
            /*CHECK IF ALL SITE UNDER THIS PRODUCT ALL CRAWLED*/
            $allCrawled = true;
            $sites = $product->sites;
            foreach ($sites as $site) {
                $excludedProductSites = $product->alert->excludedProductSites;
                $excluded = false;
                foreach ($excludedProductSites as $excludedProductSite) {
                    if ($excludedProductSite->site->getKey() == $site->getKey()) {
                        $excluded = true;
                    }
                }
                if (!$excluded) {
                    if ($site->status == "ok" && !$site->crawler->lastCrawlerWithinHour()) {
                        $allCrawled = false;
                        break;
                    }
                }
            }
            if ($allCrawled == true && !$product->alert->lastActiveWithinHour()) {
                $product->alert->last_active_at = date("Y-m-d H:i:s");
                $product->alert->save();
                switch ($product->alert->alert_owner_type) {
                    case "product":
                        $alertRepo->triggerProductAlert($product->alert);
                        break;
                    default:
                }
            }
        }
    }
}