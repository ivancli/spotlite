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
        /*disabled*/
        $this->__handleSiteAlert($this->crawler, $alertRepo);

        $this->__handleProductAlert($this->crawler, $alertRepo);

        $this->__handleCategoryAlert($this->crawler, $alertRepo);

        $this->__handleUserAlert($this->crawler, $alertRepo);

    }

    private function __handleSiteAlert(Crawler $crawler, AlertContract $alertRepo)
    {
        $site = $crawler->site;
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
    }

    private function __handleProductAlert(Crawler $crawler, AlertContract $alertRepo)
    {
        $site = $crawler->site;
        if (is_null($site)) {
            return false;
        }
        $product = $site->product;
        if (is_null($product) || is_null($product->alert)) {
            return false;
        }
        /*CHECK IF ALL SITE UNDER THIS PRODUCT ALL CRAWLED*/
        $allCrawled = true;
        $sites = $product->sites;
        foreach ($sites as $eachSite) {
            if(!$eachSite->crawler->lastActiveWithinHour() || !is_null($eachSite->crawler->status)){
                $allCrawled = false;
                break;
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

    private function __handleCategoryAlert(Crawler $crawler, AlertContract $alertRepo)
    {
        $site = $crawler->site;
        if (is_null($site)) {
            return false;
        }
        $product = $site->product;
        if (is_null($product)) {
            return false;
        }
        $category = $product->category;
        if (is_null($category) || is_null($category->alert)) {
            return false;
        }
        /*CATEGORY ALERT*/
        $allProductsCrawled = true;
        $products = $category->products;
        foreach ($products as $eachProduct) {
            foreach ($eachProduct->sites as $eachSite) {
                if(!$eachSite->crawler->lastActiveWithinHour() || !is_null($eachSite->crawler->status)){
                    $allProductsCrawled = false;
                    break;
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

    private function __handleUserAlert(Crawler $crawler, AlertContract $alertRepo)
    {
        $site = $crawler->site;
        if (is_null($site)) {
            return false;
        }
        $product = $site->product;
        if (is_null($product)) {
            return false;
        }
        $category = $product->category;
        if (is_null($category)) {
            return false;
        }

        $user = $category->user;
        if (is_null($user) || $user->alerts()->count() == 0) {
            return false;
        }

        $categories = $user->categories;
        $allCategoriesCrawled = true;
        foreach ($categories as $eachCategory) {
            $products = $eachCategory->products;
            foreach ($products as $eachProduct) {
                foreach ($eachProduct->sites as $eachSite) {
                    if (!is_null($eachProduct->alert)) {
                        if(!$eachSite->crawler->lastActiveWithinHour() || !is_null($eachSite->crawler->status)){
                            $allCategoriesCrawled = false;
                            break;
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
        if ($allCategoriesCrawled == true && (is_null($category->alert) || !$category->alert->lastActiveWithinHour())) {
            $alert = $user->alerts()->first();
            $alert->last_active_at = date("Y-m-d H:i:s");
            $alert->save();
            $alertRepo->triggerUserAlert($alert);
        }
    }

}