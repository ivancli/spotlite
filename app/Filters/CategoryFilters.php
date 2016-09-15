<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/15/2016
 * Time: 10:37 AM
 */

namespace App\Filters;


use Illuminate\Http\Request;

class CategoryFilters extends QueryFilter
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
     * search category by name
     * @param $keyWord
     * @return mixed
     */
    public function search($keyWord)
    {
        return $this->builder->where('category_name', 'LIKE', "%{$keyWord['value']}%");
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