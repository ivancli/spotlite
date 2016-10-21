<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/21/2016
 * Time: 4:49 PM
 */
class CreateTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        Session::start();
        $user = \App\Models\User::where('email', 'ivan.invigor@gmail.com')->first();
        $this->user = $user;
        $this->be($user);
    }


    public function testCreate()
    {
        $this->call('get', route('category.create'));
        $this->see("Add");
        $this->assertResponseOk();
    }
}