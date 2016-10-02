<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 3:45 PM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\Repository\Product\Domain\DomainContract;
use App\Filters\QueryFilter;
use App\Models\Domain;
use Illuminate\Http\Request;

class SLDomainManager implements DomainContract
{
    protected $domain;
    protected $request;

    public function __construct(Domain $domain, Request $request)
    {
        $this->domain = $domain;
        $this->request = $request;
    }


    public function getDomains()
    {
        $domains = $this->domain->all();
        return $domains;
    }

    public function getDomain($domain_id)
    {
        $domain = $this->domain->findOrFail($domain_id);
        return $domain;
    }

    public function getDomainByColumn($column, $value)
    {
        $domains = $this->domain->where($column, $value)->get();
        return $domains;
    }

    public function createDomain($options)
    {
        $domain = $this->domain->create($options);
        return $domain;
    }

    public function updateDomain($domain_id, $options)
    {
        $domain = $this->getDomain($domain_id);
        $domain->update($options);
        return $domain;
    }

    public function deleteDomain($domain_id)
    {
        $domain = $this->getDomain($domain_id);
        $domain->delete();
        return true;
    }

    public function getDomainCount()
    {
        return $this->domain->count();
    }

    public function getDataTableDomains(QueryFilter $queryFilter)
    {
        $domains = $this->domain->filter($queryFilter)->get();
        $output = new \stdClass();
        $output->draw = $this->request->has('draw') ? intval($this->request->get('draw')) : 0;
        $output->recordTotal = $this->getDomainCount();
        if ($this->request->has('search') && $this->request->get('search')['value'] != '') {
            $output->recordsFiltered = $domains->count();
        } else {
            $output->recordsFiltered = $this->getDomainCount();
        }
        $output->data = $domains->toArray();
        return $output;
    }
}