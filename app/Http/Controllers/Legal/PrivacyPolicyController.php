<?php

namespace App\Http\Controllers\Legal;

use App\Contracts\Repository\Legal\PrivacyPolicyContract;
use App\Validators\Legal\PrivacyPolicy\StoreValidator;
use App\Validators\Legal\PrivacyPolicy\ToggleActivenessValidator;
use App\Validators\Legal\PrivacyPolicy\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PrivacyPolicyController extends Controller
{
    protected $request;
    protected $privacyPolicyRepo;

    public function __construct(Request $request, PrivacyPolicyContract $privacyPolicyContract)
    {
        $this->middleware('permission:read_privacy_policies', ['only' => ['index']]);
        $this->middleware('permission:create_privacy_policies', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_privacy_policies', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_privacy_policies', ['only' => ['destroy']]);

        $this->request = $request;
        $this->privacyPolicyRepo = $privacyPolicyContract;
    }

    public function index()
    {
        if ($this->request->ajax()) {
            $privacyPolicies = $this->privacyPolicyRepo->all();
            $status = true;
            return response()->json(compact(['status', 'privacyPolicies']));
        } else {
            return view('legal.privacy_policy.index');
        }
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

    public function create()
    {
        return view('legal.privacy_policy.create');
    }

    public function store(StoreValidator $storeValidator)
    {
        $storeValidator->validate($this->request->all());
        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->privacyPolicyRepo->deactivateAll();
        }
        $privacyPolicy = $this->privacyPolicyRepo->store($this->request->all());
        $status = true;
        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'privacyPolicy']));
            } else {
                return compact(['status', 'privacyPolicy']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'privacyPolicy']));
        }
    }

    public function edit($id)
    {
        $privacyPolicy = $this->privacyPolicyRepo->get($id);
        if (is_null($privacyPolicy)) {
            abort(404);
            return false;
        }
        return view('legal.privacy_policy.edit')->with(compact(['privacyPolicy']));
    }

    public function update(UpdateValidator $updateValidator, $id)
    {
        $privacyPolicy = $this->privacyPolicyRepo->get($id);
        if (is_null($privacyPolicy)) {
            abort(404);
            return false;
        }

        $updateValidator->validate($this->request->all());

        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->privacyPolicyRepo->deactivateAll();
        }

        $privacyPolicy->update($this->request->all());
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'privacyPolicy']));
            } else {
                return compact(['status', 'privacyPolicy']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'privacyPolicy']));
        }
    }

    public function toggleActiveness(ToggleActivenessValidator $toggleActivenessValidator, $id)
    {
        $privacyPolicy = $this->privacyPolicyRepo->get($id);
        if (is_null($privacyPolicy)) {
            abort(404);
            return false;
        }

        $toggleActivenessValidator->validate($this->request->all());

        if ($this->request->has('active') && $this->request->get('active') == 'y') {
            $this->privacyPolicyRepo->deactivateAll();
        }

        $privacyPolicy->update($this->request->all());
        $status = true;

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json(compact(['status', 'privacyPolicy']));
            } else {
                return compact(['status', 'privacyPolicy']);
            }
        } else {
            return redirect()->back()->with(compact(['status', 'privacyPolicy']));
        }
    }

    public function destroy($id)
    {
        $privacyPolicy = $this->privacyPolicyRepo->get($id);
        if (is_null($privacyPolicy)) {
            abort(404);
            return false;
        }
        $status = $this->privacyPolicyRepo->destroy($id);

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
