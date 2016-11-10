<?php
namespace App\Contracts\Repository\Legal;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/11/2016
 * Time: 11:52 AM
 */
interface TermAndConditionContract
{
    public function get($term_and_condition_id);

    public function getActive();
}