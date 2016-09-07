<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:15 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedProduct extends Model
{
    protected $primaryKey = "deleted_product_id";
    protected $fillable = ["content"];

}