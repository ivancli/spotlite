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

    /**
     * Load all terms and conditions
     * @return mixed
     */
    public function all();

    public function get($term_and_condition_id);

    public function getActive();

    /**
     * create new term and condition
     * @param $data
     * @return mixed
     */
    public function store($data);

    public function deactivateAll();

    public function destroy($term_and_condition_id);

}