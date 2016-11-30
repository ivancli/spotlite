<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/30/2016
 * Time: 10:51 AM
 */

namespace App\Filters;


class ReportFilters extends QueryFilter
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
//        return $this->builder->where('report_owner_type', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('frequency', 'LIKE', "%{$keyWord['value']}%")
//            ->orWhere('file_type', 'LIKE', "%{$keyWord['value']}%");
        return $this->builder;
    }

    /**
     * order by columns and directions, accept multiple columns ordering
     * @param $columnsAndDirections
     * @return mixed
     */
    public function order($columnsAndDirections)
    {
//        foreach ($columnsAndDirections as $columnAndDirection) {
//            $this->builder->orderBy($columnAndDirection['column'], $columnAndDirection['dir']);
//        }
        return $this->builder;
    }
}