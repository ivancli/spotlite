<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    public $user;
    public $email;
    public $password;

    public function setUp()
    {
        parent::setUp();
        Session::start();

        $this->email = 'admin@spotlite.com.au';
        $this->password = "password_with_0987654321_!@#$%^&*()";

        $this->user = factory(App\Models\User::class)->create([
            'first_name' => 'Barack',
            'last_name' => 'Obama',
            'email' => $this->email,
            'password' => bcrypt($this->password)
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAccessWithoutLogin()
    {
        $this->visit('/')
            ->seePageIs(route('login.get'));
    }

    public function testVisitWithLogin()
    {
        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($this->user->email, 'email')
            ->type($this->password, 'password')
            ->press('Login')
            ->visit(route('login.get'))
            ->seePageIs(route('subscription.back'));
    }

    public function testRegisterLink()
    {
        $this->visit(route('login.get'))
            ->click("Don't have an account? Sign Up Now")
            ->seePageIs(route('register.get'));
    }

    public function testForgotPasswordLink()
    {
        $this->visit(route('login.get'))
            ->click("Forgot password?")
            ->seePageIs(route('password.get'));
    }

    public function testLoginFormSuccess()
    {
        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($this->email, 'email')
            ->type($this->password, 'password')
            ->press('Login')
            ->seePageIs(route('subscription.back'));
    }

    public function testLoginFormWithIncorrectPassword()
    {
        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($this->email, 'email')
            ->type('this is an incorrect password', 'password')
            ->press('Login')
            ->seePageIs(route('login.get'));
    }

    public function testLoginFormWithIncorrectEmail()
    {
        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type('random_email@example.com', 'email')
            ->type($this->password, 'password')
            ->press('Login')
            ->seePageIs(route('login.get'));
    }
}
