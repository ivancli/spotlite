<?php
namespace App\Validators\Captcha;

use App\Libraries\CommonFunctions;
use App\Validators\ValidatorAbstract;
use Illuminate\Validation\Validator;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 1/17/2017
 * Time: 4:43 PM
 */
class ReCaptchaValidator extends ValidatorAbstract
{
    use CommonFunctions;

    public function validate(array $data, $throw = true)
    {
        $response = $this->getRules();
        $response = json_decode($response);
        if ($response == false || !isset($response->success) || $response->success != true) {
            $this->throwValidationException(null);
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
        return $this->sendCurl(config('google_captcha.verification_url'), array(
            'method' => 'post',
            'fields' => array(
                'secret' => config('google_captcha.secret_key'),
                'response' => request()->get('g-recaptcha-response')
            ),
        ));
    }


    protected function formatValidationErrors(Validator $validator = null)
    {
        return array(
            "robot" => array(
                trans('validation.custom.captcha.robot'),
            )
        );
    }
}