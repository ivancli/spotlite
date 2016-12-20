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

        /*disabled*/
        /*SITE ALERT*/
        if (!is_null($site->alert) && !$site->alert->lastActiveWithinHour()) {
            $site->alert->last_active_at = date("Y-m-d H:i:s");
            $site->alert->save();
            switch ($site->alert->alert_owner_type) {
                case "site":
                    $alertRepo->triggerSiteAlert($site->alert);
                    break;
                default:
            }
        }


        $product = $site->product;

        /*PRODUCT ALERT*/
        if (!is_null($product) && !is_null($product->alert)) {
            /*CHECK IF ALL SITE UNDER THIS PRODUCT ALL CRAWLED*/
            $allCrawled = true;
            $sites = $product->sites;
            foreach ($sites as $site) {
                $excludedSites = $product->alert->excludedSites;
                $excluded = false;
                foreach ($excludedSites as $excludedSite) {
                    if ($excludedSite->getKey() == $site->getKey()) {
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

            /*CATEGORY ALERT*/
            $category = $site->product->category;
            if (!is_null($category->alert)) {
                if (!is_null($category) && !is_null($category->alert)) {
                    $allProductsCrawled = true;
                    $products = $category->products;
                    foreach ($products as $product) {
                        foreach ($product->sites as $site) {
                            $excludedSites = $product->alert->excludedSites;
                            $excluded = false;
                            foreach ($excludedSites as $excludedSite) {
                                if ($excludedSite->getKey() == $site->getKey()) {
                                    $excluded = true;
                                }
                            }
                            if (!$excluded) {
                                if ($site->status == "ok" && !$site->crawler->lastCrawlerWithinHour()) {
                                    $allProductsCrawled = false;
                                    break;
                                }
                            }
                        }
                    }
                    if ($allProductsCrawled == true && !$category->alert->lastActiveWithinHour()) {
                        $category->alert->last_active_at = date("Y-m-d H:i:s");
                        $category->alert->save();
                        switch ($category->alert->alert_owner_type) {
                            case "category":
                                $alertRepo->triggerCategoryAlert($category->alert);
                                break;
                            default:
                        }
                    }
                }
            }


            /*USER ALERT*/
            $user = $category->user;
            if ($user->alerts()->count() > 0) {
                $categories = $user->categories;
                $allCategoriesCrawled = true;
                foreach ($categories as $category) {
                    $products = $category->products;
                    foreach ($products as $product) {
                        foreach ($product->sites as $site) {
                            if(!is_null($product->alert)){
                                $excludedSites = $product->alert->excludedSites;
                                $excluded = false;
                                foreach ($excludedSites as $excludedSite) {
                                    if ($excludedSite->getKey() == $site->getKey()) {
                                        $excluded = true;
                                    }
                                }
                                if (!$excluded) {
                                    if ($site->status == "ok" && !$site->crawler->lastCrawlerWithinHour()) {
                                        $allCategoriesCrawled = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($allCategoriesCrawled == false) {
                            break;
                        }
                    }
                    if ($allCategoriesCrawled == false) {
                        break;
                    }
                }

                if ($allCategoriesCrawled == true && !is_null($category->alert) && !$category->alert->lastActiveWithinHour()) {
                    $alert = $user->alerts()->first();
                    $alert->last_active_at = date("Y-m-d H:i:s");
                    $alert->save();
                    $alertRepo->triggerUserAlert($alert);
                }
            }
        }
    }
}