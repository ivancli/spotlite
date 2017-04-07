<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/7/2017
 * Time: 9:22 AM
 */

namespace App\Console\Commands;


use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloneUser extends Command
{
    protected $signature = "clone-user {from} {to}";
    protected $description = 'Pushing available crawlers to queue';

    protected $crawler = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $fromUserId = $this->argument('from');
        $toUserId = $this->argument('to');
        $fromUser = User::findOrFail($fromUserId);
        $toUser = User::findOrFail($toUserId);

        $categories = $fromUser->categories;

        foreach ($categories as $category) {
            if (!is_null($category)) {
                $clonedCategory = $category->replicate();
                $toUser->categories()->save($clonedCategory);

                foreach ($category->products as $product) {
                    $clonedProduct = $product->replicate();
                    $clonedCategory->products()->save($clonedProduct);
                    $toUser->products()->save($clonedProduct);

                    foreach ($product->sites as $site) {
                        $clonedSite = $site->replicate();
                        $clonedProduct->sites()->save($clonedSite);
                        $clonedSite = $clonedSite->fresh(['crawler']);

                        if (isset($myCompanyDomain)) {
                            $siteDomain = parse_url($clonedSite->site_url)['host'];

                            list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
                            list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);

                            //matching both sub-domain and domain
                            if ($subdomainSplitted == $domainSplitted) {
                                $hasMyPrice = false;
                                foreach ($clonedSite->product->sites as $eachSite) {
                                    if (!is_null($eachSite->my_price) && $eachSite->my_price == 'y') {
                                        $hasMyPrice = true;
                                    }
                                }
                                if ($hasMyPrice == false) {
                                    $clonedSite->my_price = 'y';
                                    $clonedSite->save();
                                }
                            }
                        }

                        $clonedCrawlerData = $site->crawler->toArray();
                        $clonedCrawlerData['site_id'] = $clonedSite->getKey();

                        $clonedSitePreferenceData = $site->preference->toArray();
                        $clonedSitePreferenceData['site_id'] = $clonedSite->getKey();

                        $clonedSite->crawler->update($clonedCrawlerData);
                        $clonedSite->crawler->save();

                        $clonedSite->preference->update($clonedSitePreferenceData);
                        $clonedSite->preference->save();

                        $clonedHistoricalPrices = DB::select("
SELECT hp1.* FROM historical_prices hp1 JOIN 
(SELECT DATE_FORMAT(created_at, '%Y%m%d') date_date, MAX(price_id) price_id, MAX(created_at) max_date, site_id FROM historical_prices GROUP BY date_date, site_id) hp2
ON(hp1.price_id=hp2.price_id)
WHERE hp1.site_id=:site_id AND created_at >= NOW()- INTERVAL 1 QUARTER", [':site_id' => $site->getKey()]);

                        foreach ($clonedHistoricalPrices as $key => $clonedHistoricalPrice) {
                            unset($clonedHistoricalPrices[$key]->price_id);
                            $clonedHistoricalPrices[$key]->site_id = $clonedSite->getKey();
                            $clonedHistoricalPrices[$key]->crawler_id = $clonedSite->crawler->getKey();

                            $clonedHistoricalPrices[$key] = (array)$clonedHistoricalPrices[$key];
                        }

                        DB::table('historical_prices')->insert($clonedHistoricalPrices);
                    }
                }
            }
        }
    }
}