<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:29 PM
 */

namespace App\Validators\Product\Product;


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

        $this->validator->extendImplicit('unique_per_category', function ($message, $value, $parameters) use ($data) {
            $builder = auth()->user()->products();
            if (isset($data['category_id'])) {
                $builder->where('category_id', $data['category_id']);
            }
            if (is_array($parameters) && !is_null(array_first($parameters))) {
                $builder->where('product_id', '<>', array_first($parameters));
            }
            $currentProductNames = $builder->get()->map(function ($product) {
                return $product->product_name;
            })->all();
            return !in_array($value, $currentProductNames);
        });

        if (array_has($data, 'product_id')) {
            $rules = $this->getRules(array_get($data, 'product_id'));
        } else {
            $rules = $this->getRules();
        }
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
            'product_name' => "required|max:255|unique_per_category:{$id}"
        );
    }
}