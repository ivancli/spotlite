<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:06 PM
 */

namespace App\Contracts\ProductManagement;


use App\Filters\QueryFilter;

interface SiteManager
{
    public function getSites();

    public function getSite($id);

    public function getSiteByColumn($column, $value);

    public function createSite($options);

    public function updateSite($id, $options);

    public function deleteSite($id);

    public function getDataTablesSites(QueryFilter $queryFilter);
}