<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/2/2016
 * Time: 2:15 PM
 */

return array(
    'site_key' => env('GOOGLE_CAPTCHA_SITE_KEY'),
    'secret_key' => env('GOOGLE_CAPTCHA_SECRET_KEY'),
    'verification_url' => 'https://www.google.com/recaptcha/api/siteverify',
);