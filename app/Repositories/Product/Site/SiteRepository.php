<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:07 PM
 */

namespace App\Repositories\Product\Site;


use App\Contracts\Repository\Product\Domain\DomainContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Filters\QueryFilter;
use App\Libraries\CommonFunctions;
use App\Models\Product;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteRepository implements SiteContract
{
    use CommonFunctions;

    protected $site;
    protected $request;
    protected $domainRepo;

    public function __construct(Request $request, Site $site, DomainContract $domainContract)
    {
        $this->site = $site;
        $this->request = $request;
        $this->domainRepo = $domainContract;
    }

    public function getSites()
    {
        $sites = Site::all();
        return $sites;
    }

    public function getSite($id)
    {
        $site = Site::findOrFail($id);
        return $site;
    }

    public function getSiteByColumn($column, $value)
    {
        $sites = Site::where($column, $value)->get();
        return $sites;
    }

    public function createSite($options)
    {
        $options['site_url'] = $this->removeGlobalWebTracking($options['site_url']);
        $site = Site::create($options);
        return $site;
    }

    public function updateSite($id, $options)
    {
        if (isset($options['site_url'])) {
            $options['site_url'] = $this->removeGlobalWebTracking($options['site_url']);
        }
        $site = $this->getSite($id);
        $site->update($options);
        return $site;
    }

    public function deleteSite($id)
    {
        $site = $this->getSite($id);
        $site->delete();
        return true;
    }

    public function getSiteCount()
    {
        return $this->site->count();
    }

    public function getDataTablesSites(QueryFilter $queryFilter)
    {
        $sites = $this->site->with("crawler")->with('product.user')->with("preference")->filter($queryFilter)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getSiteCount();
        if ($this->request->has('status') && $this->request->get('status') != ''
            || ($this->request->has('search') && $this->request->get('search')['value'] != '')
        ) {
            $output->recordsFiltered = $sites->count();
        } else {
            $output->recordsFiltered = $this->getSiteCount();
        }
        $output->data = $sites->toArray();
        return $output;
    }

    public function adoptPreferences($site_id, $target_site_id)
    {
        $site = $this->getSite($site_id);
        $targetSite = $this->getSite($target_site_id);

        $preference = $site->preference;

        $targetPreference = $targetSite->preference;

        $preference->xpath_1 = $targetPreference->xpath_1;
        $preference->xpath_2 = $targetPreference->xpath_2;
        $preference->xpath_3 = $targetPreference->xpath_3;
        $preference->xpath_4 = $targetPreference->xpath_4;
        $preference->xpath_5 = $targetPreference->xpath_5;
        $preference->save();
    }

    public function clearPreferences($site_id)
    {
        $site = $this->getSite($site_id);
        $preference = $site->preference;
        $preference->xpath_1 = null;
        $preference->xpath_2 = null;
        $preference->xpath_3 = null;
        $preference->xpath_4 = null;
        $preference->xpath_5 = null;
        $preference->save();
    }

    public function copySiteHistoricalPrice($site_id, $target_site_id)
    {
        $site = $this->getSite($site_id);
        foreach ($site->historicalPrices as $historicalPrice) {
            $historicalPrice->delete();
        }

        $targetSite = $this->getSite($target_site_id);

        $targetHistoricalPrices = $targetSite->historicalPrices;

        foreach ($targetHistoricalPrices as $targetHistoricalPrice) {
            $historicalPrice = $targetHistoricalPrice->replicate();
            $historicalPrice->site_id = $site_id;
            $historicalPrice->crawler_id = $site->crawler->getKey();
            $historicalPrice->save();
            $historicalPrice->created_at = $targetHistoricalPrice->created_at;
            $historicalPrice->save();
        }
    }

    public function adoptDomainPreferences($site_id, $target_domain_id)
    {
        $site = $this->getSite($site_id);
        $targetDomain = $this->domainRepo->getDomain($target_domain_id);

        $preference = $site->preference;

        $targetPreference = $targetDomain->preference;

        $preference->xpath_1 = $targetPreference->xpath_1;
        $preference->xpath_2 = $targetPreference->xpath_2;
        $preference->xpath_3 = $targetPreference->xpath_3;
        $preference->xpath_4 = $targetPreference->xpath_4;
        $preference->xpath_5 = $targetPreference->xpath_5;
        $preference->save();

        $site->crawler->update(array(
            "crawler_class" => $targetDomain->crawler_class,
            "parser_class" => $targetDomain->parser_class
        ));
    }

    public function createSampleSite(Product $product)
    {
        $sampleSites = array();
        $sampleSites [] = Site::create(array(
            "product_id" => $product->getKey(),
            "site_url" => "http://www.myfirstcompetitor.com.au",
            "status" => "sample",
        ));
        $sampleSites [] = Site::create(array(
            "product_id" => $product->getKey(),
            "site_url" => "http://www.mysite.com.au",
            "status" => "sample",
        ));
    }
}