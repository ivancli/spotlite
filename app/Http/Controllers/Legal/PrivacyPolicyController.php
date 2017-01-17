<?php

namespace App\Http\Controllers\Legal;

use App\Contracts\Repository\Legal\PrivacyPolicyContract;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PrivacyPolicyController extends Controller
{
    protected $request;
    protected $privacyPolicyRepo;

    public function __construct(Request $request, PrivacyPolicyContract $privacyPolicyContract)
    {
        $this->request = $request;
        $this->privacyPolicyRepo = $privacyPolicyContract;
    }

    public function index()
    {

    }

    public function show($id)
    {
        if ($id == 0) {
            if ($this->request->ajax()) {
                if ($this->request->wantsJson()) {
                    $pp = $this->privacyPolicyRepo->getActive();
                    $status = !is_null($pp);
                    return response()->json(compact(['status', 'pp']));
                } else {
                    return view('legal.tnc_pp');
                }
            } else {
                if ($this->request->has('callback')) {
                    $pp = $this->privacyPolicyRepo->getActive();
                    $status = true;
                    return response()->json(compact(['status', 'pp']))->setCallback($this->request->get('callback'));
                }
                return view('legal.tnc_pp');
            }
        } else {

        }
    }
}
