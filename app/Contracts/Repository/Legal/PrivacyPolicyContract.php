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
    public function get($privacy_policy_id);

    public function getActive();
}