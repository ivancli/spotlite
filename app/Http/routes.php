<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['subs']], function () {
        Route::get('/', 'DashboardController@index')->name("dashboard.index");
        Route::get('msg/subscription/welcome', 'MessageController@welcomeSubscription')->name("msg.subscription.welcome");
    });

    /* Subscription related routes*/
    Route::get('verify', 'Chargify\SubscriptionController@finishPayment');
    Route::get('subscription', 'Chargify\SubscriptionController@viewAPIProducts')->name('subscribe.products');
    Route::post('subscription', 'Chargify\SubscriptionController@createSubscription')->name('subscribe.store');
});

/*Auth*/
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');

Route::get('register', 'Auth\AuthController@getRegister')->name('register.get');
Route::post('register', 'Auth\AuthController@postRegister')->name('register.post');

Route::get('logout', 'Auth\AuthController@logout')->name('logout');
