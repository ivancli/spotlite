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
        Route::get('msg/subscription/welcome/{raw?}', 'MessageController@welcomeSubscription')->name("msg.subscription.welcome");
        Route::get('msg/subscription/update/{raw?}', 'MessageController@updateSubscription')->name("msg.subscription.update");


    });

    /* User account related routes */
    Route::resource('account', 'User\AccountController');

    /* Group related routes*/
    Route::resource('group', 'User\GroupController');

    /* User profile related routes*/
    Route::get('profile/edit', 'User\ProfileController@edit')->name('profile.edit');
    Route::resource('profile', 'User\ProfileController', ['only' => [
        'index', 'show', 'update',
    ]]);


    //for those users who registered but not yet subscribe
    /* Subscription related routes*/
    Route::get('subscription/back', 'SubscriptionController@viewProducts')->name('subscription.back');
    Route::get('subscription/finalise', 'SubscriptionController@finalise')->name('subscription.finalise');
    Route::resource('subscription', 'SubscriptionController', ['except' => ['create']]);

    Route::get('msg/subscription/cancelled/{id}/{raw?}', 'MessageController@cancelledSubscription')->name("msg.subscription.cancelled");


    /*logging*/
    Route::resource('log/user_activity', 'Log\UserActivityLogController', ['only' => [
        'index', 'show'
    ]]);

});

/*Auth*/
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');

Route::get('register', 'Auth\AuthController@getRegister')->name('register.get');
Route::post('register', 'Auth\AuthController@postRegister')->name('register.post');

Route::get('logout', 'Auth\AuthController@logout')->name('logout');