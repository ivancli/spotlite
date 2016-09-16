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
            $pref->save();
        }
        return $pref;
    }

    public static function getPreference($key)
    {
        $pref = (new static)->where("element", $key)->first();
        if (!is_null($pref)) {
            return $pref->value;
        }
        return null;
    }

    /*CRAWLING settings*/

    public static function getCrawlTimes()
    {
        $crawlTimes = (new static)->getPreference("CRAWL_TIME");
        $times = explode(',', $crawlTimes);
        return $times;
    }

    public static function getCrawlReserved()
    {
        $crawlReserved = (new static)->getPreference("CRAWL_RESERVED");
        return $crawlReserved;
    }

    public static function setCrawlReserved($status = "y")
    {
        $crawlReserved = (new static)->setPreference("CRAWL_RESERVED", $status);
        return $crawlReserved;
    }

    public static function getCrawlLastReservedAt()
    {
        $crawlLastReservedAt = (new static)->getPreference("CRAWL_LAST_RESERVED_AT");
        return $crawlLastReservedAt;
    }

    public static function setCrawlLastReservedAt()
    {
        $crawlLastReservedAt = (new static)->setPreference("CRAWL_LAST_RESERVED_AT", date("Y-m-d H:i:s"));
        return $crawlLastReservedAt->value;
    }


    /* USER SYNC settings*/

    public static function getSyncTimes()
    {
        $userSyncTimes = (new static)->getPreference("SYNC_TIME");
        $times = explode(',', $userSyncTimes);
        return $times;
    }

    public static function getSyncReserved()
    {
        $syncReserved = (new static)->getPreference("SYNC_RESERVED");
        return $syncReserved;
    }

    public static function setSyncReserved($status = "y")
    {
        $syncReserved = (new static)->setPreference("SYNC_RESERVED", $status);
        return $syncReserved;
    }

    public static function getSyncLastReservedAt()
    {
        $syncLastReservedAt = (new static)->getPreference("SYNC_LAST_RESERVED_AT");
        return $syncLastReservedAt;
    }

    public static function setSyncLastReservedAt()
    {
        $syncLastReservedAt = (new static)->setPreference("SYNC_LAST_RESERVED_AT", date("Y-m-d H:i:s"));
        return $syncLastReservedAt->value;
    }
}