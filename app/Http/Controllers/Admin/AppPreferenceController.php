<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppPreference;
use App\Models\User;
use App\Repositories\Subscription\ChargifySubscriptionRepository;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 3:19 PM
 */
class AppPreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_app_preference', ['only' => ['index', 'update']]);
    }

    public function index(ChargifySubscriptionRepository $chargifySubscriptionRepository)
    {
        $appPreferences = AppPreference::all();
        return view('admin.preference.index')->with(compact(['appPreferences']));
    }

    public function update(Request $request)
    {
        $prefs = $request->get('prefs');
        foreach ($prefs as $key => $value) {
            $value = $value == "" ? null : $value;
            AppPreference::setPreference($key, $value);
        }
        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            return redirect()->route('admin.app_preference.index');
        }
    }
}