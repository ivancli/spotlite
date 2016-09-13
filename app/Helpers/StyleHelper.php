<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Request;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/08/2016
 * Time: 3:41 PM
 */
class StyleHelper
{
    public static function set_active_starts_with($str, $class = 'active')
    {
        return starts_with(Request::route()->getName(), $str) ? $class : '';
    }

    public static function set_active_or($strs, $class = 'active')
    {
        foreach ($strs as $str) {
            if (starts_with(Request::path(), $str)) {
                return $class;
            }
        }
        return '';
    }

    public static function set_active_and($strs, $class = 'active')
    {
        foreach ($strs as $str) {
            if (!in_array($str, Request::segments())) {
                return '';
            }
        }
        return $class;
    }

    public static function set_active($str, $class = 'active')
    {
        return Request::is($str) ? $class : '';
    }
}