<?php
namespace App\Validators\Product\Product;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 7:14 PM
 */
class StoreValidator extends ValidatorAbstract
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
            $currentProductNames = $builder->get()->map(function ($product) {
                return $product->product_name;
            })->all();
            return !in_array($value, $currentProductNames);
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
            "product_name" => "required|max:255|unique_per_category",
            "category_id" => "required"
        );
    }
}