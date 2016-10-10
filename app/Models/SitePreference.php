<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 9/10/2016
 * Time: 5:13 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SitePreference extends Model
{
    protected $primaryKey = "site_id";
    protected $fillable = [
        "site_id", "xpath_1", "xpath_2", "xpath_3", "xpath_4", "xpath_5"
    ];
    public $timestamps = false;

    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id', 'site_id');
    }
}