<?php
namespace App\Repositories\User\User;

use App\Contracts\Repository\User\User\UserContract;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/12/2016
 * Time: 5:00 PM
 */
class UserRepository implements UserContract
{

    public function sampleUser()
    {
        $user = auth()->user();
        switch ($user->subscription_location) {
            case "us":
                $sampleUser = User::where("email", 'us@spotlite.com.au')->first();
                break;
            case "au":
            default:
                $sampleUser = User::where("email", 'admin@spotlite.com.au')->first();
        }
        return $sampleUser;
    }

    public function updateMySite($myCompanyDomain)
    {
        $user = auth()->user();
        /*TODO problematic part*/

        $myPriceSites = $user->sites->filter(function ($site, $index) {
            return $site->mySite == 'y';
        });
        if (!is_null($myCompanyDomain)) {
            list($dummy, $domainSplitted) = explode('.', $myCompanyDomain, 2);
        } else {
            $domainSplitted = null;
        }

        if ($user->needSubscription && $user->subscription && $user->subscriptionCriteria()->my_price == false) {
            $domainSplitted = null;
        }

        foreach ($myPriceSites as $mySite) {
            $siteDomain = parse_url($mySite->site_url)['host'];
            list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
            if ($subdomainSplitted != $domainSplitted) {
                $mySite->my_price = null;
                $mySite->save();
            }
        }

        foreach ($user->sites as $site) {
            if (!is_null($domainSplitted)) {
                $siteDomain = parse_url($site->site_url)['host'];
                list($dummy, $subdomainSplitted) = explode('.', $siteDomain, 2);
                //matching both sub-domain and domain
                if ($subdomainSplitted == $domainSplitted) {
                    $hasMyPrice = false;
                    foreach ($site->product->sites as $eachSite) {
                        if (!is_null($eachSite->my_price) && $eachSite->my_price == 'y') {
                            $hasMyPrice = true;
                        }
                    }
                    if ($hasMyPrice == false) {
                        $site->my_price = 'y';
                        $site->save();
                    }
                }
            } else {
                $site->my_price = null;
                $site->save();
            }
        }
    }
}