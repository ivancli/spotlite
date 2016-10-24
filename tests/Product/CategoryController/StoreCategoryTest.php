<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/21/2016
 * Time: 4:51 PM
 */
class StoreCategoryTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $category;

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

    public function testStoreCategoryWithoutInputs()
    {
        $this->post(route('category.store'), array(), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ))
            ->assertResponseOk()
            ->seeJson(array(
                "status" => false
            ));
    }

    public function testStoreCategoryWithoutToken()
    {
        $this->post(route('category.store'), array(
            "category_name" => "test"
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
        ));
    }

    public function testStoreCategoryWithDuplicateCategoryName()
    {
        $this->post(route('category.store'), array(
            "category_name" => $this->category->category_name
        ), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "X-CSRF-TOKEN" => csrf_token(),
        ));
    }
}