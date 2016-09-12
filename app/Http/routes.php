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


        /* User profile related routes*/
        Route::get('profile/edit', 'User\ProfileController@edit')->name('profile.edit');
        Route::resource('profile', 'User\ProfileController', ['only' => [
            'index', 'show', 'update',
        ]]);
        /* Group related routes*/
        Route::get('group/first_login', 'User\GroupController@firstLogin');
        Route::resource('group', 'User\GroupController');

        /* User account related routes */
        Route::get('account/edit', 'User\AccountController@edit')->name('account.edit');
        Route::resource('account', 'User\AccountController');

        /* Product related routes */
        Route::resource('product', 'Product\ProductController');
        Route::resource('category', 'Product\CategoryController');
        Route::get('site/prices', 'Product\SiteController@getPrices')->name('site.prices');
//        Route::resource('site', 'Product\SiteController');
        Route::resource('product_site', 'Product\ProductSiteController');

        /*alert routes*/
        Route::get('alert/category/{category_id}/edit', 'Product\AlertController@editCategoryAlert')->name('alert.category.edit');
        Route::put('alert/category/{category_id}', 'Product\AlertController@updateCategoryAlert')->name('alert.category.update');
        Route::delete('alert/category/{category_id}', 'Product\AlertController@deleteCategoryAlert')->name('alert.category.destroy');
        Route::get('alert/product/{product_id}/edit', 'Product\AlertController@editProductAlert')->name('alert.product.edit');
        Route::put('alert/product/{product_id}', 'Product\AlertController@updateProductAlert')->name('alert.product.update');
        Route::delete('alert/product/{product_id}', 'Product\AlertController@deleteProductAlert')->name('alert.product.destroy');
        Route::get('alert/product_site/{product_site_id}/edit', 'Product\AlertController@editProductSiteAlert')->name('alert.product_site.edit');
        Route::put('alert/product_site/{product_site_id}', 'Product\AlertController@updateProductSiteAlert')->name('alert.product_site.update');
        Route::delete('alert/product_site/{product_site_id}', 'Product\AlertController@deleteProductSiteAlert')->name('alert.product_site.destroy');
        Route::resource('alert', 'Product\AlertController');


        /* admin crawler management */
        Route::resource('admin/product_site', 'Crawler\ProductSiteController');
    });


    //for those users who registered but not yet subscribe
    /* Subscription related routes*/
    Route::get('subscription/back', 'SubscriptionController@viewProducts')->name('subscription.back');
    Route::get('subscription/finalise', 'SubscriptionController@finalise')->name('subscription.finalise');
    Route::resource('subscription', 'SubscriptionController', ['except' => ['create']]);

    Route::get('msg/subscription/cancelled/{id}/{raw?}', 'MessageController@cancelledSubscription')->name("msg.subscription.cancelled");


    /* ADMIN */
    /* logging */
    Route::resource('log/user_activity', 'Log\UserActivityLogController', ['only' => [
        'index', 'show'
    ]]);

});

/*Auth*/
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');

Route::get('register', 'Auth\AuthController@getRegister')->name('register.get');
Route::post('register', 'Auth\AuthController@postRegister')->name('register.post');

Route::get('password', 'Auth\PasswordController@getEmail')->name('password.get');
Route::post('password', 'Auth\PasswordController@postEmail')->name('password.post');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset')->name('password.reset.get');
Route::post('password/reset', 'Auth\PasswordController@postReset')->name('password.reset.post');
Route::get('logout', 'Auth\AuthController@logout')->name('logout');