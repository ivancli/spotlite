<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\Mailer\MailerContract;
use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Jobs\SendMail;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserPreference;
use Exception;
use Illuminate\Support\Facades\Cache;
use Invigor\Chargify\Chargify;
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

    protected $subscriptionRepo;
    protected $mailerRepo;
    protected $mailingAgentRepo;
    protected $categoryRepo, $productRepo, $siteRepo;

    /**
     * Create a new authentication controller instance.
     *
     * @param SubscriptionContract $subscriptionContract
     * @param MailerContract $mailerContract
     * @param MailingAgentContract $mailingAgentContract
     * @param CategoryContract $categoryContract
     * @param ProductContract $productContract
     * @param SiteContract $siteContract
     */
    public function __construct(SubscriptionContract $subscriptionContract, MailerContract $mailerContract, MailingAgentContract $mailingAgentContract, CategoryContract $categoryContract, ProductContract $productContract, SiteContract $siteContract)
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->subscriptionRepo = $subscriptionContract;
        $this->mailerRepo = $mailerContract;
        $this->mailingAgentRepo = $mailingAgentContract;

        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;
        $this->siteRepo = $siteContract;
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
            'title' => 'min:2',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'signup_link' => 'required',
            'api_product_id' => 'required',
            'industry' => 'required',
            'company_type' => 'required',
            'company_name' => 'required',
            'agree_terms' => 'required',
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
            'title' => isset($data['title']) ? $data['title'] : null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'verification_code' => $verificationCode,
            'industry' => $data['industry'],
            'company_type' => $data['company_type'],
            'company_name' => $data['company_name'],
            'agree_terms' => $data['agree_terms'],
        ]);

        $role = UMRole::where("name", "client")->first();
        if ($role != null) {
            $user->attachRole($role);
        }

        $this->mailingAgentRepo->addSubscriber(array(
            'EmailAddress' => $user->email,
            'Name' => $user->first_name . " " . $user->last_name,
            'CustomFields' => array(
                array(
                    'Key' => 'Industry',
                    'Value' => $data['industry']
                ),
                array(
                    'Key' => 'CompanyType',
                    'Value' => $data['company_type']
                ),
                array(
                    'Key' => 'CompanyName',
                    'Value' => $data['company_name']
                ),
            )
        ));

        /*create sample products*/
        $sampleCategory = $this->categoryRepo->createSampleCategory($user);
        $sampleProduct = $this->productRepo->createSampleProduct($sampleCategory);
        $sampleSites = $this->siteRepo->createSampleSite($sampleProduct);


//        $options = $user->toArray();
//        $options['subject'] = "Welcome to SpotLite";
//        $this->dispatch((new SendMail("auth.emails.welcome", compact(['user']), $options))->onQueue("mailing"));

        if (request()->has('api_product_id')) {
            $product = Chargify::product()->get(request('api_product_id'));
            $requireCreditCard = $product->require_credit_card == true;
            $coupon_code = request()->get('coupon_code');
            if ($requireCreditCard == true) {
                /* REQUIRED CREDIT CARD */
                $reference = array(
                    "user_id" => $user->getKey(),
                    "verification_code" => $verificationCode
                );
                $encryptedReference = rawurlencode(json_encode($reference));
                $chargifyLink = $chargifyLink . "?reference=$encryptedReference&first_name={$user->first_name}&last_name={$user->last_name}&email={$user->email}&organization={$user->company_name}&coupon_code={$coupon_code}";

                $this->redirectTo = $chargifyLink;
            } else {
                /* CREDIT CARD NOT REQUIRED */

                /* create subscription in chargify */
                $fields = array(
                    "product_id" => $product->id,
                    "customer_attributes" => array(
                        "first_name" => $data['first_name'],
                        "last_name" => $data['last_name'],
                        "email" => $data['email'],
                        "organization" => $data['company_name'],
                    ),
                    "coupon_code" => $coupon_code
                );

                $result = Chargify::subscription()->create($fields);
                if (!isset($result->errors)) {
                    /* clear verification code*/
                    $user->verification_code = null;
                    $user->save();
                    try {
                        /* update subscription record */
                        $subscription = $result;
                        $expiry_datetime = $subscription->expires_at;
                        $sub = new Subscription();
                        $sub->user_id = $user->getKey();
                        $sub->api_product_id = $subscription->product_id;
                        $sub->api_customer_id = $subscription->customer_id;
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
        $productFamilies = $this->subscriptionRepo->getProductList();
        if (property_exists($this, 'registerView')) {
            return view($this->registerView)->with(compact(['productFamilies']));
        }

        return view('auth.register')->with(compact(['productFamilies']));
    }
}
