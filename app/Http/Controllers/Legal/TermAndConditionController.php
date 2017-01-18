<?php

namespace App\Http\Controllers\Legal;

use App\Contracts\Repository\Legal\TermAndConditionContract;
use App\Validators\Legal\TermAndCondition\StoreValidator;
use App\Validators\Legal\TermAndCondition\ToggleActivenessValidator;
use App\Validators\Legal\TermAndCondition\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TermAndConditionController extends Controller
{
    protected $request;
    protected $termAndConditionRepo;

    public function __construct(Request $request, TermAndConditionContract $termAndConditionContract)
    {
        $this->middleware('permission:read_terms_and_conditions', ['only' => ['index']]);
        $this->middleware('permission:create_terms_and_conditions', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_terms_and_conditions', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_terms_and_conditions', ['only' => ['destroy']]);

        $this->request = $request;
        $this->termAndConditionRepo = $termAndConditionContract;
    }

    public function index()
    {
        if ($this->request->ajax()) {
            $termsAndConditions = $this->termAndConditionRepo->all();
            $status = true;
            return response()->json(compact(['status', 'termsAndConditions']));
        } else {
            return view('legal.term_and_condition.index');
        }
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
                if ($this->request->has('callback')) {
                    $tnc = $this->termAndConditionRepo->getActive();
                    $status = !is_null($tnc);
                    return response()->json(compact(['status', 'tnc']))->setCallback($this->request->get('callback'));
                } else {
                    return view('legal.tnc_pp');
                }
            }
        } else {

        }
    }

    public function create()
    {
        return view('legal.term_and_condition.create');
    }

    public function store(StoreValidator $storeValidator)
    {
        $storeValidator->validate($this->request->all());
        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->termAndConditionRepo->deactivateAll();
        }
        $termAndCondition = $this->termAndConditionRepo->store($this->request->all());
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'termAndCondition']));
            } else {
                return compact(['status', 'termAndCondition']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'termAndCondition']));
        }
    }

    public function edit($id)
    {
        $termAndCondition = $this->termAndConditionRepo->get($id);
        if (is_null($termAndCondition)) {
            abort(404);
            return false;
        }
        return view('legal.term_and_condition.edit')->with(compact(['termAndCondition']));
    }

    public function update(UpdateValidator $updateValidator, $id)
    {
        $termAndCondition = $this->termAndConditionRepo->get($id);
        if (is_null($termAndCondition)) {
            abort(404);
            return false;
        }

        $updateValidator->validate($this->request->all());

        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->termAndConditionRepo->deactivateAll();
        }

        $termAndCondition->update($this->request->all());
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'termAndCondition']));
            } else {
                return compact(['status', 'termAndCondition']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'termAndCondition']));
        }
    }

    public function toggleActiveness(ToggleActivenessValidator $toggleActivenessValidator, $id)
    {
        $termAndCondition = $this->termAndConditionRepo->get($id);
        if (is_null($termAndCondition)) {
            abort(404);
            return false;
        }

        $toggleActivenessValidator->validate($this->request->all());

        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->termAndConditionRepo->deactivateAll();
        }

        $termAndCondition->update($this->request->all());
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'termAndCondition']));
            } else {
                return compact(['status', 'termAndCondition']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'termAndCondition']));
        }
    }

    public function destroy($id)
    {
        $termAndCondition = $this->termAndConditionRepo->get($id);
        if (is_null($termAndCondition)) {
            abort(404);
            return false;
        }
        $status = $this->termAndConditionRepo->destroy($id);

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->back()->with(compact(['status']));
        }
    }
}
