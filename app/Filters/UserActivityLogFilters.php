<?php
namespace App\Filters;
use Illuminate\Support\Facades\DB;


/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/2/2016
 * Time: 9:08 AM
 */
class UserActivityLogFilters extends QueryFilter
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
        return $this->builder->where('activity', 'LIKE', "%{$keyWord['value']}%")
            ->orWhereHas('owner', function ($query) use ($keyWord) {
                $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), "LIKE", "%{$keyWord['value']}%");
            });
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