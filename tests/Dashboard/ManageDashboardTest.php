<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/21/2016
 * Time: 11:55 AM
 */
class ManageDashboardTest extends TestCase
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

    public function testVisitManageDashboard()
    {
        $this->visit(route('dashboard.manage'))
            ->see("Manage Dashboard")
            ->assertResponseOk();
    }
}