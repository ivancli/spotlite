<?php
namespace App\Repositories\Legal;

use App\Contracts\Repository\Legal\TermAndConditionContract;
use App\Models\Legal\TermAndCondition;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/11/2016
 * Time: 11:53 AM
 */
class TermAndConditionRepository implements TermAndConditionContract
{
    protected $termAndCondition;

    public function __construct(TermAndCondition $termAndCondition)
    {
        $this->termAndCondition = $termAndCondition;
    }

    public function get($term_and_condition_id)
    {
        return $this->termAndCondition->findOrFail($term_and_condition_id);
    }

    public function getActive()
    {
        return $this->termAndCondition->where('active', 'y')->first();
    }
}