<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 10:39 AM
 */
class DefaultDashboardTest extends TestCase
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

    public function testVisitDefaultDashboard()
    {
        $this->visit(route('dashboard.index'))
            ->see("Default")
            ->assertResponseOk();
    }
}