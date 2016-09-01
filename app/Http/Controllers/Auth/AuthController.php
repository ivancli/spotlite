<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Models\Subscription;
use App\User;
use Exception;
use Invigor\UM\UMRole;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $username = 'email';

    protected $subscriptionManager;

    /**
     * Create a new authentication controller instance.
     *
     * @param SubscriptionManager $subscriptionManager
     */
    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $chargifyLink = request('signup_link');
        $verificationCode = str_random(10);
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'verification_code' => $verificationCode
        ]);
        $role = UMRole::where("name", "client")->first();
        if ($role != null) {
            $user->attachRole($role);
        }
        if (request()->has('api_product_id')) {
            $product = $this->subscriptionManager->getProduct(request('api_product_id'));
            $requireCreditCard = $product->require_credit_card == true;

            if ($requireCreditCard == true) {
                /* REQUIRED CREDIT CARD */
                $reference = array(
                    "user_id" => $user->getKey(),
                    "verification_code" => $verificationCode
                );
                $encryptedReference = rawurlencode(json_encode($reference));
                $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}";

                $this->redirectTo = $chargifyLink;
            } else {
                /* CREDIT CARD NOT REQUIRED */

                /* create subscription in chargify */
                $fields = new \stdClass();
                $subscription = new \stdClass();
                $subscription->product_id = $product->id;
                $customer_attributes = new \stdClass();
                $customer_attributes->first_name = $data['first_name'];
                $customer_attributes->last_name = $data['last_name'];
                $customer_attributes->email = $data['email'];
                $subscription->customer_attributes = $customer_attributes;
                $fields->subscription = $subscription;

                $result = $this->subscriptionManager->storeSubscription(json_encode($fields));
                if ($result != null) {
                    /* clear verification code*/
                    $user->verification_code = null;
                    $user->save();
                    try {
                        /* update subscription record */
                        $subscription = $result->subscription;
                        $expiry_datetime = $subscription->expires_at;
                        $sub = new Subscription();
                        $sub->user_id = $user->getKey();
                        $sub->api_product_id = $subscription->product->id;
                        $sub->api_customer_id = $subscription->customer->id;
                        $sub->api_subscription_id = $subscription->id;
                        $sub->expiry_date = date('Y-m-d H:i:s', strtotime($expiry_datetime));
                        $sub->save();
                        $this->redirectTo = route('msg.subscription.welcome');
                    } catch (Exception $e) {
                        return $user;
                    }
                }
            }
        }
        return $user;
    }

    public function showRegistrationForm()
    {
        $products = $this->subscriptionManager->getProducts();

        if (property_exists($this, 'registerView')) {
            return view($this->registerView)->with(compact(['products']));
        }

        return view('auth.register')->with(compact(['products']));
    }
}
