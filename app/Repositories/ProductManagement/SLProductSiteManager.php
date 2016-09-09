<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 1:19 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\ProductSiteManager;
use App\Models\ProductSite;

class SLProductSiteManager implements ProductSiteManager
{

    public function getProductSites()
    {
        $productSites = ProductSite::all();
        return $productSites;
    }

    public function getProductSite($product_site_id)
    {
        $productSite = ProductSite::findOrFail($product_site_id);
        return $productSite;
    }

    public function storeProductSite($options)
    {
        $productSite = ProductSite::create($options);
        return $productSite;
    }

    public function updateProductSite($product_site_id, $options)
    {
        $productSite = $this->getProductSite($product_site_id);
        $productSite->update($options);
        return $productSite;
    }

    public function deleteProductSite($product_site_id)
    {
        $productSite = $this->getProductSite($product_site_id);
        $productSite->delete();
        return true;
    }
}