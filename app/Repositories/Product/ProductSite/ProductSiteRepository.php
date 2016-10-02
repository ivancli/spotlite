<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 1:19 PM
 */

namespace App\Repositories\Product\ProductSite;


use App\Contracts\Repository\Product\ProductSite\ProductSiteContract;
use App\Filters\QueryFilter;
use App\Models\ProductSite;
use Illuminate\Http\Request;

class ProductSiteRepository implements ProductSiteContract
{
    protected $productSite;
    protected $request;

    public function __construct(ProductSite $productSite, Request $request)
    {
        $this->productSite = $productSite;
        $this->request = $request;
    }

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

    public function getProductSiteCount(){
        return $this->productSite->count();
    }

    public function getDataTablesProductSites(QueryFilter $queryFilter)
    {
        $productSites = $this->productSite->filter($queryFilter)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getProductSiteCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $productSites->count();
        } else {
            $output->recordsFiltered = $this->getProductSiteCount();
        }
        $output->data = $productSites->toArray();
        return $output;
    }
}