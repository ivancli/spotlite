<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/19/2016
 * Time: 3:15 PM
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{

    public function massUpdatePreferences(Request $request)
    {
        $preferences = $request->get('preferences');
        foreach ($preferences as $key => $preference) {
            UserPreference::setPreference(auth()->user(), $key, $preference);
        }

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status']));
            } else {
                return compact(['status']);
            }
        } else {
            /*TODO implement this if necessary*/
        }
    }

    public function updatePreference(Request $request, $element, $value)
    {
        $user = auth()->user();
        $preference = UserPreference::setPreference($user, $element, $value);

        $status = true;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['preference', 'status']));
            } else {
                return compact(['preference', 'status']);
            }
        } else {
            /*TODO implement this if needed*/
        }
    }
}