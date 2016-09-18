<?php
namespace App\Validators\Product\Category;
use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:08 PM
 */
class StoreValidator extends ValidatorAbstract
{

    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return array(
            "category_name" => "required|max:255"
        );
    }

    protected function getMessages()
    {
        return array(
            "category_name.required" => "Category name is required.",
            "category_name.max" => "Category name accepts maximum 255 characters."
        );
    }
}