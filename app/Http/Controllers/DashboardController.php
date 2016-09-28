<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/29/2016
 * Time: 10:28 AM
 */

namespace App\Http\Controllers;


use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Libraries\CommonFunctions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{
    protected $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    public function index(Request $request)
    {
        if($request->has('helloworld')){
            $host = 'http://localhost:4444/wd/hub'; // this is the default
            $capabilities = DesiredCapabilities::firefox();
            $driver = RemoteWebDriver::create($host, $capabilities, 5000, 500000);

// navigate to 'http://docs.seleniumhq.org/'
            $driver->get('http://www.google.com.au');

            echo "Page source: ";
            $driver->wait(10, 500)->until(
                WebDriverExpectedCondition::titleIs('My Page')
            );

            $html = $driver->getPageSource();

            $driver->quit();
            dd($html);
        }

        return redirect()->route("product.index");
//        return view('dashboard.index');
    }
}