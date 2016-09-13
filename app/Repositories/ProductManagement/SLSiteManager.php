<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:07 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\SiteManager;
use App\Filters\QueryFilter;
use App\Models\Site;
use Illuminate\Http\Request;

class SLSiteManager implements SiteManager
{
    protected $site;
    protected $request;

    public function __construct(Request $request, Site $site)
    {
        $this->site = $site;
        $this->request = $request;
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
        $site = Site::where("site_url", $options['site_url'])->where(function ($query) use ($options) {
            if (isset($options['site_xpath'])) {
                $query->where('site_xpath', $options['site_xpath']);
            } else {
                $query->whereNull('site_xpath');
            }
        })->first();
        if (is_null($site)) {
            $site = Site::create($options);
        }
        return $site;
    }

    public function updateSite($id, $options)
    {
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
        $sites = $this->site->with("crawler")->filter($queryFilter)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getSiteCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $sites->count();
        } else {
            $output->recordsFiltered = $this->getSiteCount();
        }
        $output->data = $sites->toArray();
        return $output;
    }
}