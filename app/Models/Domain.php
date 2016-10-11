<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 2:48 PM
 */

namespace App\Models;


use App\Filters\QueryFilter;
use App\Models\DeletedRecordModels\DeletedDomain;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $primaryKey = "domain_id";
    protected $fillable = [
        "domain_url", "domain_name", "domain_xpath", "cookie_id", "crawler_class", "parser_class"
    ];
    protected $appends = ['urls'];
    public $timestamps = false;

    public function getUrlsAttribute()
    {
        return array(
//            "edit" => route("admin.domain.edit", $this->getKey()),
            "update" => route("admin.domain.update", $this->getKey()),
            "delete" => route("admin.domain.destroy", $this->getKey()),
            "xpath_edit" => route("admin.domain.xpath.edit", $this->getKey()),
        );
    }

    public function preference()
    {
        return $this->hasOne('App\Models\DomainPreference', 'domain_id', 'domain_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function delete()
    {
        DeletedDomain::create(array(
            "content" => $this->toJson()
        ));
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public static function create(array $attributes = [])
    {
        $domain = parent::create($attributes); // TODO: Change the autogenerated stub

        /* create one-to-one site preference when site is created */
        DomainPreference::create(array(
            "domain_id" => $domain->getKey()
        ));

        return $domain;
    }
}