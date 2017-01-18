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

    public function all()
    {
        return $this->privacyPolicy->all();
    }

    public function get($privacy_policy_id)
    {
        return $this->privacyPolicy->findOrFail($privacy_policy_id);
    }

    public function getActive()
    {
        return $this->privacyPolicy->where('active', 'y')->first();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return PrivacyPolicy::create($data);
    }

    public function deactivateAll()
    {
        $privacyPolicies = PrivacyPolicy::where('active', 'y')->get();
        foreach ($privacyPolicies as $privacyPolicy) {
            $privacyPolicy->active = 'n';
            $privacyPolicy->save();
        }
    }

    public function destroy($privacy_policy_id)
    {
        $privacyPolicy = $this->get($privacy_policy_id);
        $privacyPolicy->delete();
        return true;
    }
}