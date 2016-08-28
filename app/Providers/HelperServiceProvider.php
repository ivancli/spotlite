<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/08/2016
 * Time: 3:34 PM
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path().'/Helpers/*.php') as $filename){
            require_once($filename);
        }
    }
}