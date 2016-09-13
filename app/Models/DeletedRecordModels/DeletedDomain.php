<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/13/2016
 * Time: 5:30 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedDomain extends Model
{
    protected $primaryKey = "deleted_domain_id";
    protected $fillable = ["content"];
}