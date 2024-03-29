<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/23/2016
 * Time: 3:12 PM
 */

namespace App\Http\Controllers;


use App\Jobs\SendMail;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function notifyError()
    {
        $input = $this->request->all();
        $host = "unknown page";
        if (isset($input['url']) && !empty($input['url']) && parse_url($input['url']) !== false && isset(parse_url($input['url'])['host'])) {
            $host = parse_url($input['url'])['host'];
        }
//        dispatch((new SendMail('errors.email.front_end', compact(['input']), array(
//            "email" => config('error_notifier.email'),
//            "subject" => 'Error on ' . $host,
//        )))->onQueue("mailing"));
    }
}