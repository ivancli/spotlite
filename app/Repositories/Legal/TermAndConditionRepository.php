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

    /**
     * Load all terms and conditions
     * @return mixed
     */
    public function all()
    {
        return $this->termAndCondition->all();
    }

    public function get($term_and_condition_id)
    {
        return $this->termAndCondition->findOrFail($term_and_condition_id);
    }

    public function getActive()
    {
        return $this->termAndCondition->where('active', 'y')->first();
    }

    /**
     * create new term and condition
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return TermAndCondition::create($data);
    }

    public function deactivateAll()
    {
        $termsAndConditions = TermAndCondition::where('active', 'y')->get();
        foreach ($termsAndConditions as $termAndCondition) {
            $termAndCondition->active = 'n';
            $termAndCondition->save();
        }
    }

    public function destroy($term_and_condition_id)
    {
        $termAndCondition = $this->get($term_and_condition_id);
        $termAndCondition->delete();
        return true;
    }
}