<?php
namespace App\Validators;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 2:05 PM
 */
use App\Exceptions\ValidationException;
use Illuminate\Validation\Factory as IlluminateValidator;

abstract class ValidatorAbstract
{
    protected $validator;

    public function __construct(IlluminateValidator $validator)
    {
        $this->validator = $validator;
    }

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
        $rules = $this->getRules();
        $messages = $this->getMessages();
        $validation = $this->validator->make($data, $rules, $messages);
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
    abstract protected function getRules($id = null);

    protected function getMessages()
    {
        return [];
    }
}