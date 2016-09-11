<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/8/2016
 * Time: 12:43 PM
 */

namespace App\Models;


use App\Models\DeletedRecordModels\DeletedAlert;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $primaryKey = "alert_id";
    protected $fillable = [
        "alert_owner_id", "alert_owner_type", "comparison_price_type", "comparison_price", "comparison_site_id", "operator"
    ];
    public $timestamps = false;
    protected $appends = ["urls"];

    public function alertable()
    {
        return $this->morphTo("alert_owner", "alert_owner_type");
    }

    public function excludedProductSites()
    {
        return $this->belongsToMany('App\Models\ProductSite', 'alert_excluded_product_sites', 'alert_id', 'product_site_id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\AlertEmail', 'alert_id', 'alert_id');
    }

    /**
     * back up alert before deleting
     * @return bool|null
     */
    public function delete()
    {
        /* delete alert emails if there are any */
        foreach ($this->emails as $email) {
            $email->delete();
        }
        DeletedAlert::create(array(
            "content" => $this->toJson()
        ));
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("alert.show", $this->getKey()),
            "edit" => route("alert.edit", $this->getKey()),
            "delete" => route("alert.destroy", $this->getKey()),
        );
    }
}