<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
    ];
});

$factory->define(App\Models\Subscription::class, function(Faker\Generator $faker){
    return [
        "api_product_id" => 123456789,
        "api_customer_id" => 123456789,
        "api_subscription_id" => 123456789
    ];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker) {
    return [
        "user_id"=> 1,
        "category_name"=> str_random(10),
    ];
});

$factory->define(App\Models\Product::class, function (Faker\Generator $faker) {
    return [
        "product_name"=> str_random(10),
        "user_id" => 1,
    ];
});


$factory->define(App\Models\Site::class, function (Faker\Generator $faker) {
    return [
        "site_url"=> "http://www.myer.com.au/shop/mystore/home-appliances/dyson-213552-01-small-ball-allergy-upright-vacuum-cleaner%3A-nickel-satin-blue",
        "site_xpath" => '//*[@id="WC_CachedProductOnlyDisplay_div_4"]/span',
    ];
});

$factory->define(App\Models\Crawler::class, function (Faker\Generator $faker) {
    return [
    ];
});
