<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\Mailer\MailerContract;
use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\Product\Category\CategoryContract;
use App\Contracts\Repository\Product\Product\ProductContract;
use App\Contracts\Repository\Product\Site\SiteContract;
use App\Contracts\Repository\Security\TokenContract;
use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Jobs\SendMail;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserPreference;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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
    protected $categoryRepo, $productRepo, $siteRepo, $tokenRepo;

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
    public function __construct(SubscriptionContract $subscriptionContract, MailerContract $mailerContract, MailingAgentContract $mailingAgentContract, CategoryContract $categoryContract, ProductContract $productContract, SiteContract $siteContract, TokenContract $tokenContract)
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->subscriptionRepo = $subscriptionContract;
        $this->mailerRepo = $mailerContract;
        $this->mailingAgentRepo = $mailingAgentContract;

        $this->categoryRepo = $categoryContract;
        $this->productRepo = $productContract;
        $this->siteRepo = $siteContract;
        $this->tokenRepo = $tokenContract;
    }

    public function showRegistrationForm()
    {
        $productFamilies = $this->subscriptionRepo->getProductList();
        if (property_exists($this, 'registerView')) {
            return view($this->registerView)->with(compact(['productFamilies']));
        }

        return view('auth.register')->with(compact(['productFamilies']));
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
            'api_product_id' => 'required',
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

        $referer = request()->server('HTTP_REFERER');
        $referer = parse_url($referer);
        $path = $referer['path'];
        $subscriptionLocation = strpos($path, 'us') !== false ? 'us' : 'au';
        $user = User::create([
            'title' => isset($data['title']) ? $data['title'] : '',
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'verification_code' => $verificationCode,
            'agree_terms' => $data['agree_terms'],
            'set_password' => isset($data['set_password']) ? $data['set_password'] : 'y',
            'subscription_location' => $subscriptionLocation,
        ]);

        $role = UMRole::where("name", "client")->first();
        if ($role != null) {
            $user->attachRole($role);
        }

        $this->mailingAgentRepo->addSubscriber(array(
            'EmailAddress' => $user->email,
            'Name' => $user->first_name . " " . $user->last_name,
        ));

        if (request()->has('api_product_id')) {
            $product = Chargify::product($subscriptionLocation)->get(request('api_product_id'));
            $coupon_code = isset($data['coupon']) ? $data['coupon'] : '';
            $reference = array(
                "user_id" => $user->getKey(),
                "verification_code" => $verificationCode
            );
            $encryptedReference = json_encode($reference);
            /* create subscription in chargify */
            $fields = array(
                "product_id" => $product->id,
                "customer_attributes" => array(
                    "first_name" => $data['first_name'],
                    "last_name" => $data['last_name'],
                    "email" => $data['email'],
                    "reference" => $encryptedReference,
                    "country" => array_has($data, 'country') && array_get($data, 'country') != '' ? $data['country'] : "AU",
                    "state" => array_has($data, 'state') && array_get($data, 'state') != '' ? $data['state'] : 'NSW',
                ),
                "coupon_code" => $coupon_code
            );

            $result = Chargify::subscription($subscriptionLocation)->create($fields);
            if (!isset($result->errors)) {
                /* clear verification code*/
                $user->verification_code = null;
                $user->save();
                try {
                    /* update subscription record */
                    $subscription = $result;
                    $subscription = $user->subscription()->save(new Subscription([
                        'api_product_id' => $subscription->product_id,
                        'api_customer_id' => $subscription->customer_id,
                        'api_subscription_id' => $subscription->id,
                        'subscription_location' => $subscriptionLocation,
                    ]));
                    $user->clearAllCache();

                    $criteria = json_decode($product->description);

                    $this->mailingAgentRepo->editSubscriber($user->email, array(
                        "CustomFields" => array(
                            array(
                                "Key" => "SubscribedDate",
                                "Value" => date("Y/m/d")
                            ),
                            array(
                                "Key" => "SubscriptionPlan",
                                "Value" => $product->name
                            ),
                            array(
                                "Key" => "TrialExpiry",
                                "Value" => date('Y/m/d', strtotime($subscription->trial_ended_at))
                            ),
                            array(
                                "Key" => "NumberofSites",
                                "Value" => 0
                            ),
                            array(
                                "Key" => "NumberofProducts",
                                "Value" => 0
                            ),
                            array(
                                "Key" => "NumberofCategories",
                                "Value" => 0
                            ),
                            array(
                                "Key" => "MaximumNumberofProducts",
                                "Value" => isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
                            ),
                            array(
                                "Key" => "MaximumNumberofSites",
                                "Value" => isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
                            ),
                            array(
                                "Key" => "LastLoginDate",
                                "Value" => date('Y/m/d')
                            ),
                            array(
                                "Key" => "SubscriptionCancelledDate",
                                "Value" => null
                            ),
                            array(
                                "Key" => "CancelledBeforeEndofTrial",
                                "Value" => null
                            ),
                            array(
                                "Key" => "CancelledAfterEndofTrial",
                                "Value" => null
                            ),
                            array(
                                "Key" => "SubscriptionLocation",
                                "Value" => $subscriptionLocation
                            )
                        )
                    ));


                } catch (Exception $e) {
                    $user->clearAllCache();
                    return $user;
                }
            }
        }
        $user->clearAllCache();
        return $user;
    }

    protected function externalValidator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'api_product_id' => 'required',
            'agree_terms' => 'required',
        ]);
    }

    protected function registerExternal()
    {
        $request = request();

        if (!$request->has('_token') || !$this->tokenRepo->verifyToken($request->get('_token'))) {
            $status = false;
            $errors = array(
                ['Session has expired please refresh and try again.']
            );
            if ($request->has('callback')) {
                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
            } else if ($request->wantsJson()) {
                return response()->json(compact(['errors', 'status']));
            } else {
                return compact(['errors', 'status']);
            }
        }

        $validator = $this->externalValidator($request->all());

        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors();
            if ($request->has('callback')) {
                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
            } else if ($request->wantsJson()) {
                return response()->json(compact(['errors', 'status']));
            } else {
                return compact(['errors', 'status']);
            }
        }

        $input = $request->all();
        $input['password'] = bcrypt("secret");
        $input['set_password'] = 'n';

        Auth::guard($this->getGuard())->login($this->create($input));


        $redirectPath = $this->redirectPath();
        $status = true;

        if ($request->has('callback')) {
            return response()->json(compact(['redirectPath', 'status']))->setCallback($request->get('callback'));
        } else if ($request->wantsJson()) {
            return response()->json(compact(['redirectPath', 'status']));
        } else {
            return compact(['redirectPath', 'status']);
        }
    }

    protected function registerExternalPreview()
    {
        $request = request();

        if (!$request->has('_token') || !$this->tokenRepo->verifyToken($request->get('_token'))) {
            $status = false;
            $errors = array(
                ['Session has expired please refresh and try again.']
            );


            if ($request->has('callback')) {
                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
            } else if ($request->wantsJson()) {
                return response()->json(compact(['errors', 'status']));
            } else {
                return compact(['errors', 'status']);
            }
        }

        $validator = $this->externalValidator($request->all());

        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors();
            if ($request->has('callback')) {
                return response()->json(compact(['errors', 'status']))->setCallback($request->get('callback'));
            } else if ($request->wantsJson()) {
                return response()->json(compact(['errors', 'status']));
            } else {
                return compact(['errors', 'status']);
            }
        }

        $input = $request->all();
        $input['password'] = bcrypt("secret");
        $input['set_password'] = 'n';

        Auth::guard($this->getGuard())->login($this->create($input));


        $redirectPath = $this->redirectPath();
        $status = true;

        if ($request->has('callback')) {
            return response()->json(compact(['redirectPath', 'status']))->setCallback($request->get('callback'));
        } else if ($request->wantsJson()) {
            return response()->json(compact(['redirectPath', 'status']));
        } else {
            return compact(['redirectPath', 'status']);
        }
    }
}
