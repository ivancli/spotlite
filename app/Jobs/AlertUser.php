<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 5:34 PM
 */

namespace App\Jobs;


use App\Contracts\ProductManagement\AlertManager;
use App\Models\Alert;
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
     * @param AlertManager $alertManager
     */
    public function handle(AlertManager $alertManager)
    {
        $site = $this->crawler->site;

        $productSites = $site->productSites;
        foreach ($productSites as $productSite) {
            switch ($productSite->alert['alert_owner_type']) {
                case "product_site":
                    $alertManager->triggerProductSiteAlert($productSite->alert);
                    break;
                case "product":
                    $alertManager->triggerProductAlert($productSite->alert);
                    break;
                default:
            }
        }
    }
}