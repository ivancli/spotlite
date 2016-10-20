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
            ->see("Default Dashboard")
            ->assertResponseOk();
    }

    public function testVisitManageDashboard()
    {
        $this->visit(route('dashboard.manage'))
            ->see("Manage Dashboard")
            ->assertResponseOk();
    }

//    public function testCreateWidget()
//    {
//        $this->withoutMiddleware();
//
//        $email = 'admin@spotlite.com.au';
//        $password = "password_with_0987654321_!@#$%^&*()";
//
//        $user = App\Models\User::create([
//            'first_name' => 'Barack',
//            'last_name' => 'Obama',
//            'email' => $email,
//            'password' => bcrypt($password)
//        ]);
//
//        $this->actingAs($user)
//            ->visit(route('dashboard.index'))
//            ->press('Add Content')
//            ->see('Content Characteristics');
//    }
}