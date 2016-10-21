<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11/10/2016
 * Time: 11:20 PM
 */
class RegisterTest extends TestCase
{
    use DatabaseTransactions;
    protected $user;

    protected $email;
    protected $password;

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

    public function testOpenRegisterPage()
    {
        $this->visit(route('register.get'))
            ->assertResponseOk()
            ->see("Choose a subscription plan");
    }

    public function testVisitRegisterPageWithUserLoggedIn()
    {
        $this->be($this->user);
        $this->visit(route("register.get"))
            ->assertResponseStatus(302);
    }

    public function testSubmitRegistrationWithoutToken()
    {
        $this->call('POST', route('register.post'), [
        ]);
        $this->assertResponseStatus(403);
    }

    public function testSubmitRegistrationWithoutData()
    {
        $this->call('POST', route('register.post'), [
            "_token" => csrf_token()
        ]);
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();
    }

    public function testSubmitRegistrationWithoutEmail()
    {
        $faker = app(Faker\Generator::class);
        $this->call('POST', route('register.post'), [
            'title' => "Mr.",
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'password' => $faker->password(),
            'signup_link' => "https://www.google.com.au",
            'component_id' => 1,
            'family_id' => 1,
            'api_product_id' => 1,
            "_token" => csrf_token()
        ]);
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();
    }
}