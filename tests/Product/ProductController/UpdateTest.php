<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 5:42 PM
 */
class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public $user;
    public $category;
    public $product;

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

        $this->product = \App\Models\Product::create(array(
            "user_id" => $this->user->getKey(),
            "category_id" => $this->category->getKey(),
            "product_name" => str_random(20)
        ));
    }

    public function testUpdateProductWithoutProductName()
    {
        $this->put(route('product.update', $this->product->getKey()), array(), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testUpdateProductWithTooLongProductName()
    {
        $this->put(route('product.update', $this->product->getKey()), array(
            "product_name" => str_random(256)
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

    public function testUpdateNonExistProduct()
    {
        $this->put(route('product.update', 0), array(
            "product_name" => str_random(10)
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseStatus(404);
    }

    /* TODO update product correctly and check update result */
}