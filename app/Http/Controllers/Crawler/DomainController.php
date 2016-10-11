<?php

namespace App\Http\Controllers\Crawler;

use App\Contracts\Repository\Product\Domain\DomainContract;
use App\Filters\QueryFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

class DomainController extends Controller
{
    protected $domainRepo;
    protected $queryFilter;

    public function __construct(DomainContract $domainContract, QueryFilter $queryFilter)
    {
        $this->domainRepo = $domainContract;
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
            $domains = $this->domainRepo->getDataTableDomains($this->queryFilter);
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

        $domain = $this->domainRepo->createDomain($request->all());
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*TODO validation here*/
        $domain = $this->domainRepo->getDomain($id);
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
     * @param Request $request
     * @param $site_id
     * @return SiteController|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editxPath(Request $request, $site_id)
    {
        $domain = $this->domainRepo->getDomain($site_id);
        if ($request->ajax()) {
            if ($request->wantsJson()) {

            } else {
                return view('admin.domain.forms.xpath')->with(compact(['domain']));
            }
        } else {
            return view('admin.domain.forms.xpath')->with(compact(['domain']));
        }
    }

    public function updatexPath(Request $request, $site_id)
    {
        $input = array_map(function ($e) {
            return $e ?: null;
        }, $request->all());

        $domain = $this->domainRepo->getDomain($site_id);
        $domain->preference->update($input);
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /* TODO implement this if necessary */
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
        $domain = $this->domainRepo->getDomain($id);
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
