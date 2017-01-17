<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Model;

class TermAndCondition extends Model
{
    protected $table = "terms_and_conditions";
    protected $primaryKey = "term_and_condition_id";
    protected $fillable = [
        'content', 'active'
    ];
    protected $appends = ["urls"];

    public function setActive()
    {
        $termsAndConditions = (new static)->all();
        foreach ($termsAndConditions as $termAndCondition) {
            $termAndCondition->setInactive();
        }
        $this->active = 'y';
        $this->save();
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
            "show" => route("term_and_condition.show", $key),
            "edit" => route("term_and_condition.edit", $key),
            "update" => route("term_and_condition.update", $key),
            "delete" => route("term_and_condition.destroy", $key),
        );
    }
}
