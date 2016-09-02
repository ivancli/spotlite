<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/2/2016
 * Time: 9:10 AM
 */

namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value], function ($value) {
                    return ($value !== null && $value !== false && $value !== '');
                }));
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}