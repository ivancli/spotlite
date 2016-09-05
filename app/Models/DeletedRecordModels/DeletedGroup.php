<?php
namespace App\Models\DeletedRecordModels;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 12:59 PM
 */
class DeletedGroup extends Model
{
    protected $primaryKey = "deleted_group_id";
    protected $fillable = ["content"];

}