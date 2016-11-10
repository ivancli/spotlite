<?php

namespace App\Http\Controllers\Legal;

use App\Contracts\Repository\Legal\TermAndConditionContract;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TermAndConditionController extends Controller
{
    protected $request;
    protected $termAndConditionRepo;

    public function __construct(Request $request, TermAndConditionContract $termAndConditionContract)
    {
        $this->request = $request;
        $this->termAndConditionRepo = $termAndConditionContract;
    }

    public function show($id)
    {
        if ($id == 0) {
            if ($this->request->ajax()) {

                if ($this->request->wantsJson()) {
                    $tnc = $this->termAndConditionRepo->getActive();
                    $status = !is_null($tnc);
                    return response()->json(compact(['status', 'tnc']));
                } else {
                    return view('legal.tnc_pp');
                }
            } else {
                return view('legal.tnc_pp');
            }
        } else {

        }
    }
}
