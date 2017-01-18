<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    protected $table = "privacy_policies";
    protected $primaryKey = "privacy_policy_id";
    protected $fillable = [
        'content', 'active'
    ];
    protected $appends = ["urls"];

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

    public function getUrlsAttribute()
    {
        $key = $this->getKey();
        return array(
            "show" => route("privacy_policy.show", $key),
            "edit" => route("privacy_policy.edit", $key),
            "update" => route("privacy_policy.update", $key),
            "delete" => route("privacy_policy.destroy", $key),
            "activeness" => route('privacy_policy.activeness', $key),
        );
    }
}
