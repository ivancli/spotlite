<?php
namespace App\Validators;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/09/2016
 * Time: 2:05 PM
 */
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Factory as IlluminateValidator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

abstract class ValidatorAbstract
{
    protected $validator;

    protected $validatesRequestErrorBag;

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
    abstract protected function getRules($id = null);

    protected function getMessages()
    {
        return [];
    }

    protected function throwValidationException($validator)
    {
        throw new ValidationException($validator, $this->buildFailedValidationResponse($this->formatValidationErrors($validator)));
    }


    protected function buildFailedValidationResponse(array $errors)
    {
        $request = request();
        if (($request->ajax() && !$request->pjax()) || $request->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }

    protected function getRedirectUrl()
    {
        return app(UrlGenerator::class)->previous();
    }

    protected function errorBag()
    {
        return $this->validatesRequestErrorBag ?: 'default';
    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->getMessages();
    }
}