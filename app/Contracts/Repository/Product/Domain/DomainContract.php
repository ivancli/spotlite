<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 3:43 PM
 */

namespace App\Contracts\Repository\Product\Domain;


use App\Filters\QueryFilter;

interface DomainContract
{
    public function getDomains();

    public function getDomain($domain_id);

    public function getDomainByColumn($column, $value);

    public function createDomain($options);

    public function updateDomain($domain_id, $options);

    public function deleteDomain($domain_id);

    public function getDataTableDomains(QueryFilter $queryFilter);
}