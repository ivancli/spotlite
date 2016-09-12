<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 12/09/2016
 * Time: 10:26 PM
 */

namespace App\Filters;


class AdminProductSiteFilter extends QueryFilter
{

    /**
     * Setting the offset of the query
     * @param $numberOfRows
     * @return mixed
     */
    public function start($numberOfRows)
    {
        return $this->builder->skip($numberOfRows);
    }

    /**
     * Setting the length of each page
     * @param $numberOfRows
     * @return mixed
     */
    public function length($numberOfRows)
    {
        return $this->builder->take($numberOfRows);
    }

    /**
     * search logs by activity and first name + last name
     * @param $keyWord
     * @return mixed
     */
    public function search($keyWord)
    {
        return $this->builder
            ->join('sites', 'sites.site_id', '=', 'product_sites.site_id')
            ->where('product_site_id', 'LIKE', "%{$keyWord['value']}%")
            ->orWhere('sites.site_url', 'LIKE', "%{$keyWord['value']}%");
//        $query->where("site_url", "LIKE", "%{$keyWord['value']}%");
    }

    /**
     * order by columns and directions, accept multiple columns ordering
     * @param $columnsAndDirections
     * @return mixed
     */
    public function order($columnsAndDirections)
    {
        foreach ($columnsAndDirections as $columnAndDirection) {
            $this->builder->orderBy($columnAndDirection['column'], $columnAndDirection['dir']);
        }
        return $this->builder;
    }
}