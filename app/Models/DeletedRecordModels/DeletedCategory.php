<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 2:12 PM
 */

namespace App\Models\DeletedRecordModels;


use Illuminate\Database\Eloquent\Model;

class DeletedCategory extends Model
{
    protected $primaryKey = "deleted_category_id";
    protected $fillable = ["content"];
}