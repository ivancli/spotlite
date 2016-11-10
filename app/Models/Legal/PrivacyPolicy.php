<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    protected $primaryKey = "privacy_policy_id";
    protected $fillable = [
        'content', 'active'
    ];

    public function setActive()
    {
        $privacyPolicies = (new static)->all();
        foreach ($privacyPolicies as $privacyPolicy) {
            $privacyPolicy->setInactive();
        }
    }

    public function setInactive()
    {
        $this->active = 'n';
        $this->save();
    }
}
