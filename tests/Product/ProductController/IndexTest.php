<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 2:51 PM
 */
class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        Session::start();

        $user = \App\Models\User::where('email', 'ivan.invigor@gmail.com')->first();
        $this->be($user);
    }

    public function testAjaxJson()
    {
        $this->get(route('product.index'), array(
            "X-Requested-With" => "XMLHttpRequest",
            "Accept" => "application/json, text/javascript, */*; q=0.01"
        ))
            ->assertResponseOk()
            ->seeJson();
    }

    public function testAjaxHTML()
    {
        $this->get(route('product.index'), array(
            "X-Requested-With" => "XMLHttpRequest"
        ))
            ->assertResponseOk();
    }

    public function testHTML()
    {
        $this->get(route('product.index'))
            ->assertResponseOk()
            ->see('Add Category');
    }
}