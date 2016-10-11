<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/11/2016
 * Time: 1:23 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class DomainPreference extends Model
{
    protected $primaryKey = "domain_id";
    protected $fillable = [
        "domain_id", "xpath_1", "xpath_2", "xpath_3", "xpath_4", "xpath_5"
    ];
    public $timestamps = false;

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain', 'domain_id', 'domain_id');
    }
}