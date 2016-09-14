<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/14/2016
 * Time: 3:10 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AppPreference extends Model
{
    protected $primaryKey = "app_preference_id";
    protected $fillable = [
        "element", "value"
    ];

    public static function setPreference($key, $value)
    {
        $pref = (new static)->where("element", $key)->first();
        if (is_null($pref)) {
            $pref = (new static)->create(array(
                "element" => $key,
                "value" => $value
            ));
        } else {
            $pref->value = $value;
        }
        return $pref;
    }

    public static function getPreference($key)
    {
        $pref = (new static)->where("element", $key)->first();
        return $pref->value;
    }


    public static function getCrawlTimes()
    {
        $crawlTimes = (new static)->getPreference("CRAWL_TIME");
        $times = explode(',', $crawlTimes);
        return $times;
    }
}