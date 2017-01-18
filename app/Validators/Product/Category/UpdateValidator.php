<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:11 PM
 */

namespace App\Validators\Product\Category;


use App\Validators\ValidatorAbstract;

class UpdateValidator extends ValidatorAbstract
{
    /**
     * Validate data with provided validation rules
     *
     * @param array $data
     * @param bool $throw
     * @return bool|\Illuminate\Support\MessageBag
     * @throws ValidationException
     */
    public function validate(array $data, $throw = true)
    {
        /*TODO enhance this shit, move the extension to service provider and make it dynamic based on parameters*/
        $this->validator->extendImplicit('unique_per_user', function ($message, $value, $rule, $parameters) use ($data) {
            if (isset($data['category_id'])) {
                $currentCategoryNames = auth()->user()->categories()->where('category_id', '!=', $data['category_id'])->pluck("category_name")->toArray();
            } else {
                $currentCategoryNames = auth()->user()->categories->pluck("category_name")->toArray();
            }

            return !in_array($value, $currentCategoryNames);
        });

        $rules = $this->getRules();
        $messages = $this->getMessages();
        $validation = $this->validator->make($data, $rules, $messages);
        if ($validation->fails()) {
            if ($throw) {
                $this->throwValidationException($validation);
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
            "category_name" => "required|max:255|unique_per_user"
        );
    }
}