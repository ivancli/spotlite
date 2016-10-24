<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 5:16 PM
 */
class ShowProductTest extends TestCase
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

    public function testShowNotExistProduct()
    {
        $this->get(route('product.show', 0))
            ->assertResponseStatus(404);
    }

    public function testAjaxShowJSONProduct()
    {
        $this->get(route('product.show', $this->product->getKey()), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01"
        ))
            ->assertResponseOk()
            ->seeJson(
                array(
                    'status' => true
                )
            );
    }

    public function testAjaxShowHTMLProduct()
    {
        $this->get(route('product.show', $this->product->getKey()), array(
            "X-Requested-With" => "XMLHttpRequest",
        ))
            ->assertResponseOk()
            ->see($this->product->product_name);
    }

    public function testShowHTMLProduct()
    {
        $this->visit(route('product.show', $this->product->getKey()))
            ->assertResponseOk()
            ->see($this->product->product_name);
    }
}