<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 1:18 PM
 */

namespace App\Contracts\ProductManagement;


use App\Filters\QueryFilter;

interface ProductSiteManager
{
    public function getProductSites();

    public function getProductSite($product_site_id);

    public function storeProductSite($options);

    public function updateProductSite($product_site_id, $options);

    public function deleteProductSite($product_site_id);

    public function getDataTablesProductSites(QueryFilter $queryFilter);
}