<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

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
        $email = 'admin@spotlite.com.au';
        $password = "password_with_0987654321_!@#$%^&*()";

        $user = factory(App\Models\User::class)->create([
            'first_name' => 'Barack',
            'last_name' => 'Obama',
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($email, 'email')
            ->type($password, 'password')
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
        $email = 'admin@spotlite.com.au';
        $password = "password_with_0987654321_!@#$%^&*()";

        $user = factory(App\Models\User::class)->create([
            'first_name' => 'Barack',
            'last_name' => 'Obama',
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($email, 'email')
            ->type($password, 'password')
            ->press('Login')
            ->seePageIs(route('subscription.back'));
    }

    public function testLoginFormFail()
    {
        $email = 'admin@spotlite.com.au';
        $password = "password_with_0987654321_!@#$%^&*()";

        $user = factory(App\Models\User::class)->create([
            'first_name' => 'Barack',
            'last_name' => 'Obama',
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        /**
         * expect success login and redirection to subscription page
         */
        $this->visit(route('login.get'))
            ->type($email, 'email')
            ->press('Login')
            ->seePageIs(route('login.get'));
    }
}
