<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:06 PM
 */

namespace App\Contracts\Repository\Product\Site;


use App\Filters\QueryFilter;
use App\Models\Product;

interface SiteContract
{
    public function getSites();

    public function getSite($id);

    public function getSiteByColumn($column, $value);

    public function getSitesByProduct(Product $product);

    public function createSite($options);

    public function updateSite($id, $options);

    public function deleteSite($id);

    public function adoptPreferences($site_id, $target_site_id);

    public function adoptDomainPreferences($site_id, $target_domain_id);

    public function clearPreferences($site_id);

    public function copySiteHistoricalPrice($site_id, $target_site_id);

    public function getDataTablesSites(QueryFilter $queryFilter);

    public function createSampleSite(Product $product);
}