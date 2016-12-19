<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/20/2016
 * Time: 9:33 AM
 */

namespace App\Http\Controllers;


use App\Models\Unsubscriber;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create($page)
    {
        if (!$this->request->has('email')) {
            abort(403);
            return false;
        }

        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $this->request->get('email'))) {
            abort(403);
            return FALSE;
        }

        $email = base64_decode($this->request->get('email'));
        return view('unsubscribe.confirm')->with(compact(['page', 'email']));
    }

    public function store()
    {
        if (!$this->request->has('email')) {
            abort(403);
            return false;
        }

        $input = $this->request->all();
        $email = $input['email'];
        if (Unsubscriber::where('email', $input['email'])->count() == 0) {
            $unsubscriber = Unsubscriber::create($input);
        }
        $status = true;

        return view('unsubscribe.result')->with(compact(['status', 'email']));
    }
}