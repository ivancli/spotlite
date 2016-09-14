<?php

namespace App\Http\Controllers\Crawler;

use App\Contracts\ProductManagement\DomainManager;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

class DomainController extends Controller
{
    protected $domainManager;
    protected $queryFilter;

    public function __construct(DomainManager $domainManager, QueryFilter $queryFilter)
    {
        $this->domainManager = $domainManager;
        $this->queryFilter = $queryFilter;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $domains = $this->domainManager->getDataTableDomains($this->queryFilter);
            if ($request->wantsJson()) {
                return response()->json($domains);
            } else {
                return $domains;
            }
        } else {
            return view('admin.domain.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.domain.forms.add_domain');
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*TODO validation here*/

        $domain = $this->domainManager->createDomain($request->all());
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['domain', 'status']));
            } else {
                return compact(['domain', 'status']);
            }
        } else {
            return redirect()->route('admin.domain.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*TODO validation here*/
        $domain = $this->domainManager->getDomain($id);
        $domain->update($request->all());
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'domain']));
            } else {
                return compact(['status', 'domain']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $domain = $this->domainManager->getDomain($id);
        $domain->delete();
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('admin.domain.index');
        }
    }
}
