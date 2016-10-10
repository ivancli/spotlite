<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 12:51 PM
 */

namespace App\Filters;


class AdminSiteFilters extends QueryFilter
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
        return $this->builder->where(function ($query) use ($keyWord) {
            $query->where('site_url', 'LIKE', "%{$keyWord['value']}%")
                ->orWhere('site_id', 'LIKE', "%{$keyWord['value']}%")
                ->orWhere('recent_price', 'LIKE', "%{$keyWord['value']}%")
                ->orWhere('last_crawled_at', 'LIKE', "%{$keyWord['value']}%");
        });
//        return $this->builder->where('site_url', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('site_id', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('site_xpath', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('recent_price', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('last_crawled_at', 'LIKE', "%{$keyWord['value']}%");
    }

    public function status($status)
    {
        return $this->builder->where('status', $status);
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