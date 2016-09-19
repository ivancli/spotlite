<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/19/2016
 * Time: 3:05 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $primaryKey = "user_preference_id";
    protected $fillable = [
        "user_id", "element", "value"
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public static function setPreference(User $user, $key, $value)
    {
        $pref = (new static)->where("element", $key)->where('user_id', $user->getKey())->first();
        if (is_null($pref)) {
            $pref = (new static)->create(array(
                "user_id" => $user->getKey(),
                "element" => $key,
                "value" => $value
            ));
        } else {
            $pref->value = $value;
            $pref->save();
        }
        return $pref;
    }

    public static function getPreference(User $user, $key)
    {
        $pref = (new static)->where("element", $key)->where('user_id', $user->getKey())->first();
        if (!is_null($pref)) {
            return $pref->value;
        }
        return null;
    }

}