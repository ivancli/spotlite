<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 1:55 PM
 */

namespace App\Models;


use App\Models\DeletedRecordModels\DeletedGroup;
use Invigor\UM\UMGroup;

class Group extends UMGroup
{
    public function delete()
    {
        DeletedGroup::create(array(
            "content" => $this->toJson()
        ));
        return parent::delete(); // TODO: Change the autogenerated stub
    }
}