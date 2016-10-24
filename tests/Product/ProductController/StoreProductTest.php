<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 3:39 PM
 */
class StoreProductTest extends TestCase
{
    use DatabaseTransactions;

    public $user;
    public $category;

    public function setUp()
    {
        parent::setUp();
        Session::start();
        $user = \App\Models\User::where('email', 'ivan.invigor@gmail.com')->first();
        $this->user = $user;
        $this->be($user);

        $this->category = \App\Models\Category::create(array(
            "user_id" => $this->user->getKey(),
            "category_name" => "random category"
        ));
    }

    public function testStoreProductWithoutInputFields()
    {
        $this->post(route('product.store'), array(), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testStoreProductWithoutProductName()
    {
        $this->post(route('product.store'), array(
            "category_id" => $this->category->getKey(),
            "user_id" => $this->user->getKey()
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testStoreProductWithTooLongProductName()
    {

        $this->post(route('product.store'), array(
            "product_name" => str_random(256),
            "category_id" => $this->category->getKey(),
            "user_id" => $this->user->getKey()
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testStoreProductWithoutCategoryId()
    {
        $this->post(route('product.store'), array(
            "product_name" => "testing product"
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testStoreProductCorrectly()
    {
        $this->post(route('product.store'), array(
            "category_id" => $this->category->getKey(),
            "product_name" => "testing product",
            "user_id" => $this->user->getKey()
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => true
            ));
    }
}