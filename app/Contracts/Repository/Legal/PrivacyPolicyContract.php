<?php
namespace App\Contracts\Repository\Legal;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/11/2016
 * Time: 11:52 AM
 */
interface PrivacyPolicyContract
{
    /**
     * Load all terms and conditions
     * @return mixed
     */
    public function all();

    public function get($privacy_policy_id);

    public function getActive();

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    public function deactivateAll();

    public function destroy($term_and_condition_id);
}