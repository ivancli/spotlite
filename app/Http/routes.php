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
        Route::get('msg/subscription/cc_expiring/{raw?}', 'MessageController@notifyCreditCardExpiringSoon')->name('msg.subscription.cc_expiring');


        /**
         * User Profile Related Routes
         */
        Route::get('profile/edit', 'User\ProfileController@edit')->name('profile.edit');
        Route::resource('profile', 'User\ProfileController', ['except' => [
            'create', 'store', 'destroy', 'edit'
        ]]);


        /**
         * Group Related Routes
         */
        Route::get('group/first_login', 'User\GroupController@firstLogin');
        Route::resource('group', 'User\GroupController');


        /**
         * User Account Related Routes
         */
//        Route::get('account/edit', 'User\AccountController@edit')->name('account.edit');
        Route::resource('account', 'User\AccountController', ['except' => [
            'create', 'store', 'destroy', 'edit'
        ]]);

        /**
         * User Preferences Related Routes
         */
        Route::put('preference/{element}/{value}', 'User\UserPreferenceController@updatePreference')->name('preference.update');


        /**
         * Product Related Routes
         */
        //product routes
        Route::put('product/order', 'Product\ProductController@updateOrder')->name('product.order');
        Route::resource('product', 'Product\ProductController', ['except' => [
            'edit'
        ]]);
        //category routes
        Route::put('category/order', 'Product\CategoryController@updateOrder')->name('category.order');
        Route::resource('category', 'Product\CategoryController', ['except' => [
            'index', 'edit'
        ]]);
        //site routes
        Route::get('site/prices', 'Product\SiteController@getPrices')->name('site.prices');
        //product site routes
        Route::put("product_site/{product_site_id}/my_price", 'Product\ProductSiteController@setMyPrice')->name('product_site.my_price');
        Route::resource('product_site', 'Product\ProductSiteController', ['except' => [
            'index'
        ]]);


        /**
         * Alert Related Routes
         */
        Route::get('alert/category/{category_id}/edit', 'Product\AlertController@editCategoryAlert')->name('alert.category.edit');
        Route::put('alert/category/{category_id}', 'Product\AlertController@updateCategoryAlert')->name('alert.category.update');
        Route::delete('alert/category/{category_id}', 'Product\AlertController@deleteCategoryAlert')->name('alert.category.destroy');
        Route::get('alert/product/{product_id}/edit', 'Product\AlertController@editProductAlert')->name('alert.product.edit');
        Route::put('alert/product/{product_id}', 'Product\AlertController@updateProductAlert')->name('alert.product.update');
        Route::delete('alert/product/{product_id}', 'Product\AlertController@deleteProductAlert')->name('alert.product.destroy');
        Route::get('alert/product_site/{product_site_id}/edit', 'Product\AlertController@editProductSiteAlert')->name('alert.product_site.edit');
        Route::put('alert/product_site/{product_site_id}', 'Product\AlertController@updateProductSiteAlert')->name('alert.product_site.update');
        Route::delete('alert/product_site/{product_site_id}', 'Product\AlertController@deleteProductSiteAlert')->name('alert.product_site.destroy');


        /**
         * Chart Related Routes
         */
        Route::get('chart/category/{category_id}', 'Product\ChartController@categoryIndex')->name('chart.category.index');
        Route::get('chart/product/{product_id}', 'Product\ChartController@productIndex')->name('chart.product.index');
        Route::get('chart/product_site/{product_site_id}', 'Product\ChartController@productSiteIndex')->name('chart.product_site.index');
    });


    /**
     * Subscription Related Routes
     */
    //for those users who registered but not yet subscribe
    Route::get('subscription/back', 'SubscriptionController@viewProducts')->name('subscription.back');
    //redirect route for chargify sign up page
    Route::get('subscription/finalise', 'SubscriptionController@finalise')->name('subscription.finalise');
    Route::get('subscription/update', 'SubscriptionController@externalUpdate')->name('subscription.external_update');
    Route::resource('subscription', 'SubscriptionController', ['except' => [
        'create', 'show'
    ]]);


    Route::get('msg/subscription/cancelled/{id}/{raw?}', 'MessageController@cancelledSubscription')->name("msg.subscription.cancelled");


    /* ADMIN */
    /* logging */
    Route::resource('log/user_activity', 'Log\UserActivityLogController', ['only' => [
        'index', 'show'
    ]]);

    /* admin crawler management */
    Route::post('admin/site/test/{site_id}', 'Crawler\SiteController@sendTest')->name('admin.site.test');
    Route::resource('admin/site', 'Crawler\SiteController', ['except' => [
        'show', 'edit'
    ]]);
    Route::resource('admin/crawler', 'Crawler\CrawlerController', ['only' => [
        'edit', 'update'
    ]]);
    Route::resource('admin/domain', 'Crawler\DomainController', ['except' => [
        'show', 'edit'
    ]]);


    Route::get('logout', 'Auth\AuthController@logout')->name('logout');
});

/**
 * Auth Related Routes
 */
Route::get('login', 'Auth\AuthController@getLogin')->name('login.get');
Route::post('login', 'Auth\AuthController@postLogin')->name('login.post');

Route::get('register', 'Auth\AuthController@getRegister')->name('register.get');
Route::post('register', 'Auth\AuthController@postRegister')->name('register.post');

Route::get('password', 'Auth\PasswordController@getEmail')->name('password.get');
Route::post('password', 'Auth\PasswordController@postEmail')->name('password.post');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset')->name('password.reset.get');
Route::post('password/reset', 'Auth\PasswordController@postReset')->name('password.reset.post');