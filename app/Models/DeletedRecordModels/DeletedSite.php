<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:08 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedSite extends Model
{
    protected $primaryKey = "deleted_site_id";
    protected $fillable = ["content"];
}