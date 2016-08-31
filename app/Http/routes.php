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
        Route::get('msg/subscription/update', 'MessageController@updateSubscription')->name("msg.subscription.update");
        Route::resource('subscription', 'SubscriptionController');
        Route::put('c_subscription/{id}', 'Chargify\APISubscriptionController@updateSubscription')->name('chargify.subscribe.update');
    });

    Route::get('msg/subscription/cancelled/{id}', 'MessageController@cancelledSubscription')->name("msg.subscription.cancelled");

    /* User account related routes */
    Route::resource('account', 'User\AccountController');
    Route::resource('profile', 'User\ProfileController');

    /* Subscription related routes*/
    Route::get('verify', 'Chargify\APISubscriptionController@finishPayment');
    Route::get('c_subscription', 'Chargify\APISubscriptionController@viewAPIProducts')->name('chargify.subscribe.products');
    Route::post('c_subscription', 'Chargify\APISubscriptionController@createSubscription')->name('chargify.subscribe.store');
    Route::delete('c_subscription/{id}', 'Chargify\APISubscriptionController@cancelSubscription')->name('chargify.subscribe.cancel');
});

/*Auth*/
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');

Route::get('register', 'Auth\AuthController@getRegister')->name('register.get');
Route::post('register', 'Auth\AuthController@postRegister')->name('register.post');

Route::get('logout', 'Auth\AuthController@logout')->name('logout');