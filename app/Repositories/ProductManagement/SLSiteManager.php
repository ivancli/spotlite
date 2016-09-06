<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:07 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\SiteManager;
use App\Models\Site;

class SLSiteManager implements SiteManager
{

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

    public function createSite($options)
    {
        $site = Site::create($options);
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
}