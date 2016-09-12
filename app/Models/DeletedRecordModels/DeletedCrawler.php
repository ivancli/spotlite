<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/12/2016
 * Time: 5:21 PM
 */

namespace App\Models\DeletedRecordModels;


class DeletedCrawler
{
    protected $primaryKey = "deleted_crawler_id";
    protected $fillable = ["content"];
}