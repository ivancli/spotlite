<?php
namespace App\Repositories\Legal;

use App\Contracts\Repository\Legal\PrivacyPolicyContract;
use App\Models\Legal\PrivacyPolicy;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/11/2016
 * Time: 11:53 AM
 */
class PrivacyPolicyRepository implements PrivacyPolicyContract
{
    protected $privacyPolicy;

    public function __construct(PrivacyPolicy $privacyPolicy)
    {
        $this->privacyPolicy = $privacyPolicy;
    }

    public function get($privacy_policy_id)
    {
        $this->privacyPolicy->findOrFail($privacy_policy_id);
    }

    public function getActive()
    {
        return $this->privacyPolicy->where('active', 'y')->first();
    }
}