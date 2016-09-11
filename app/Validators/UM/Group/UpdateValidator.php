<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:58 PM
 */

namespace App\Validators\UM\Group;


use App\Exceptions\ValidationException;
use App\Validators\ValidatorAbstract;

class UpdateValidator extends ValidatorAbstract
{
    public function validate(array $data, $throw = true)
    {
        $rules = $this->getRules(isset($data['id']) ? $data['id'] : null);
        $validation = $this->validator->make($data, $rules);
        if ($validation->fails()) {
            if ($throw) {
                throw new ValidationException($validation->messages());
            } else {
                return $validation->messages();
            }
        }
        return true;
    }


    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return array(
            'name' => 'required|max:255|min:1|unique:groups,name,' . $id . ',group_id',
            'active' => 'boolean',
            'url' => 'required|url|max:2083|min:1',
            'description' => 'max: 2048'
        );
    }
}