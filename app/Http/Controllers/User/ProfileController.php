<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\Dashboard\DashboardWidgetContract;
use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\User\User\UserContract;
use App\Events\User\Profile\ProfileEditViewed;
use App\Events\User\Profile\ProfileUpdated;
use App\Events\User\Profile\ProfileUpdating;
use App\Events\User\Profile\ProfileViewed;
use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\HistoricalPrice;
use App\Models\User;
use App\Validators\User\Profile\InitUpdateValidator;
use App\Validators\User\Profile\UpdateValidator;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $mailingAgentRepo;
    protected $userRepo;
    protected $dashboardWidgetRepo;

    public function __construct(MailingAgentContract $mailingAgentContract, UserContract $userContract, DashboardWidgetContract $dashboardWidgetContract)
    {
        $this->middleware('permission:read_user', ['only' => ['show']]);
        $this->mailingAgentRepo = $mailingAgentContract;
        $this->userRepo = $userContract;
        $this->dashboardWidgetRepo = $dashboardWidgetContract;
    }

    /**
     * Edit page of my profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        event(new ProfileViewed($user));
        return view('user.profile.index')->with(compact(['user']));
    }

    public function edit()
    {
        $user = auth()->user();
        event(new ProfileEditViewed($user));
        return view('user.profile.edit')->with(compact(['user']));
    }

    /**
     * Detail page of other users' profile
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        event(new ProfileViewed($user));
        return view('user.profile.show')->with(compact(['user']));
    }

    /**
     * Update my profile
     * @param UpdateValidator $updateValidator
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateValidator $updateValidator, Request $request, $id)
    {
        $updateValidator->validate($request->all());

        $user = User::findOrFail($id);
        event(new ProfileUpdating($user));
        $input = array_except($request->all(), ['email']);
        $user->update($input);
        event(new ProfileUpdated($user));

        $this->mailingAgentRepo->editSubscriber($user->email, array(
            'Name' => $user->first_name . " " . $user->last_name,
        ));

        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'user']));
            } else {
                return compact(['status', 'user']);
            }
        } else {
            return redirect()->route("profile.index");
        }
    }

    public function initialUpdate(InitUpdateValidator $initUpdateValidator, Request $request)
    {
        try {
            $initUpdateValidator->validate($request->all());
        } catch (ValidationException $e) {
            $status = false;
            $errors = $e->getErrors();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return compact(['status', 'errors']);
                }
            } else {
                return redirect()->back()->withInput()->withErrors($errors);
            }
        }

        $user = auth()->user();
        event(new ProfileUpdating($user));
        $input = array_except($request->all(), ['email']);
        $user->update($input);
        event(new ProfileUpdated($user));


        /*set my price*/
        $companyURL = auth()->user()->company_url;
        if (!is_null($companyURL) && !empty($companyURL)) {
            $myCompanyDomain = parse_url($companyURL)['host'];
        }

        $sampleUser = $this->userRepo->sampleUser();
        if (auth()->user()->getKey() != $sampleUser->getKey()) {
            $industry = $input['industry'];
            if (isset($input['category']) && !empty($input['category'])) {
                $selectedCategories = $input['category'];
                foreach ($selectedCategories as $selectedCategory) {
                    $category = $sampleUser->categories()->where('category_name', $selectedCategory)->first();
                    if (!is_null($category)) {
                        $clonedCategory = $category->replicate();
                        $clonedCategory->user_id = auth()->user()->getKey();
                        $clonedCategory->save();

                        foreach ($category->products as $product) {
                            $clonedProduct = $product->replicate();
                            $clonedProduct->category_id = $clonedCategory->getKey();
                            $clonedProduct->user_id = auth()->user()->getKey();
                            $clonedProduct->save();

                            foreach ($product->sites as $site) {
                                $clonedSite = $site->replicate();
                                $clonedSite->product_id = $clonedProduct->getKey();
                                $clonedSite->save();
                                $clonedSite = $clonedSite->fresh(['crawler']);

                                if (isset($myCompanyDomain)) {
                                    $siteDomain = parse_url($clonedSite->site_url)['host'];

                                    list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
                                    list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);

                                    //matching both sub-domain and domain
                                    if ($subdomainSplitted == $domainSplitted) {
                                        $hasMyPrice = false;
                                        foreach ($clonedSite->product->sites as $eachSite) {
                                            if (!is_null($eachSite->my_price) && $eachSite->my_price == 'y') {
                                                $hasMyPrice = true;
                                            }
                                        }
                                        if ($hasMyPrice == false) {
                                            $clonedSite->my_price = 'y';
                                            $clonedSite->save();
                                        }
                                    }
                                }

                                $clonedCrawlerData = $site->crawler->toArray();
                                $clonedCrawlerData['site_id'] = $clonedSite->getKey();

                                $clonedSitePreferenceData = $site->preference->toArray();
                                $clonedSitePreferenceData['site_id'] = $clonedSite->getKey();

                                $clonedSite->crawler->update($clonedCrawlerData);
                                $clonedSite->crawler->save();

                                $clonedSite->preference->update($clonedSitePreferenceData);
                                $clonedSite->preference->save();

                                $clonedHistoricalPrices = DB::select("
SELECT hp1.* FROM historical_prices hp1 JOIN 
(SELECT DATE_FORMAT(created_at, '%Y%m%d') date_date, MAX(price_id) price_id, MAX(created_at) max_date, site_id FROM historical_prices GROUP BY date_date, site_id) hp2
ON(hp1.price_id=hp2.price_id)
WHERE hp1.site_id=:site_id AND created_at >= NOW()- INTERVAL 1 QUARTER", [':site_id' => $site->getKey()]);

                                foreach ($clonedHistoricalPrices as $key => $clonedHistoricalPrice) {
                                    unset($clonedHistoricalPrices[$key]->price_id);
                                    $clonedHistoricalPrices[$key]->site_id = $clonedSite->getKey();
                                    $clonedHistoricalPrices[$key]->crawler_id = $clonedSite->crawler->getKey();

                                    $clonedHistoricalPrices[$key] = (array)$clonedHistoricalPrices[$key];
                                }

                                DB::table('historical_prices')->insert($clonedHistoricalPrices);

                                /*generating SITE CHARTS*/
                                $firstNonHiddenDashboard = auth()->user()->nonHiddenDashboards()->first();
                                if (!is_null($firstNonHiddenDashboard)) {
                                    $dashboardWidget = $this->dashboardWidgetRepo->storeWidget(array(
                                        "dashboard_id" => $firstNonHiddenDashboard->getKey(),
                                        "dashboard_widget_name" => $clonedSite->domain,
                                        "timespan" => "this_week",
                                        "resolution" => "daily",
                                        "dashboard_widget_type_id" => 1,
                                        "category_id" => $clonedCategory->getKey(),
                                        "product_id" => $clonedProduct->getKey(),
                                        "site_id" => $clonedSite->getKey(),
                                        "chart_type" => "site",
                                    ));

                                    $dashboardWidget->setPreference("chart_type", "site");
                                    $dashboardWidget->setPreference("site_id", $clonedSite->getKey());
                                    $dashboardWidget->setPreference("product_id", $clonedProduct->getKey());
                                    $dashboardWidget->setPreference("category_id", $clonedCategory->getKey());
                                    $dashboardWidget->setPreference("timespan", "this_week");
                                    $dashboardWidget->setPreference("resolution", "daily");
                                }
                            }
                        }
                    }
                }
            }

        }

        $this->mailingAgentRepo->editSubscriber($user->email, array(
            'Name' => $user->first_name . " " . $user->last_name,
        ));

        if ($request->ajax()) {
            $status = true;
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'user']));
            } else {
                return compact(['status', 'user']);
            }
        } else {
            return redirect()->route("profile.index");
        }
    }
}
