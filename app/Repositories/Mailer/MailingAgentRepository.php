<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/4/2016
 * Time: 2:17 PM
 */

namespace App\Repositories\Mailer;


use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Contracts\Repository\Subscription\SubscriptionContract;
use App\Models\User;
use Invigor\Chargify\Chargify;

class MailingAgentRepository implements MailingAgentContract
{
    protected $listId;
    protected $auth;

    public function __construct()
    {
        $this->auth = array(
            'api_key' => config("campaign_monitor.api_key")
        );
        $this->listId = config("campaign_monitor.list_id");
    }

    public function getSubscriber($email)
    {
        $wrap = new \CS_REST_Subscribers($this->listId, $this->auth);
        $result = $wrap->get($email);
        if ($result->http_status_code == 200) {
            $subscriber = $result->response;
            return $subscriber;
        } else {
            /*fail*/
            /* ideally log error */
            return null;
        }
    }

    public function addSubscriber($fields)
    {
        $wrap = new \CS_REST_Subscribers($this->listId, $this->auth);
        $result = $wrap->add($fields);
        if ($result->http_status_code == 201) {
            $email = $result->response;
            return $email;
        } else {
            /*fail*/
            /* ideally log error */
            return null;
        }
    }

    public function editSubscriber($email, $fields)
    {
        $wrap = new \CS_REST_Subscribers($this->listId, $this->auth);
        $result = $wrap->update($email, $fields);
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function deleteSubscriber($email)
    {
        $wrap = new \CS_REST_Subscribers($this->listId, $this->auth);
        $result = $wrap->delete($email);
        if ($result->http_status_code == 200) {
            return true;
        } else {
            return $result->response;
        }
    }

    public function unsubscribe($email)
    {
        $wrap = new \CS_REST_Subscribers($this->listId, $this->auth);
        $result = $wrap->unsubscribe($email);
        if ($result->http_status_code == 200) {
            return true;
        } else {
            return $result->response;
        }
    }

    public function updateNumberOfSites()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "NumberofSites",
                    "Value" => $user->sites()->count()
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateNumberOfProducts()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "NumberofProducts",
                    "Value" => $user->products()->count()
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateNumberOfCategories()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "NumberofCategories",
                    "Value" => $user->categories()->count()
                )
            )
        ));
        if (isset($result->http_status_code) && $result->http_status_code == 200) {
            return $result;
        } elseif (isset($result->Message)) {
            return $result->Message;
        } else {
            return $result->response;
        }
    }

    public function updateLastAddCategoryDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddedCategoryDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if (isset($result->http_status_code) && $result->http_status_code == 200) {
            return $result;
        } elseif (isset($result->Message)) {
            return $result->Message;
        } else {
            return $result->response;
        }
    }

    public function updateLastAddProductDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddedProductDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateLastAddSiteDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddedSiteDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateLastNominatedMyPriceDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastNominatedMySiteDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateLastSetupAlertDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastSetupAlertDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateLastSetupReportDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastSetupReportDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function syncUserSubscription(User $user)
    {
        if ($user->needSubscription && !is_null($user->subscription)) {
            $subscription = $user->apiSubscription;
            if ($subscription != false) {
                $criteria = $user->subscriptionCriteria();
            }
            $subscriber = $this->getSubscriber($user->email);
            if (is_null($subscriber)) {
                $this->addSubscriber(array(
                    'EmailAddress' => $user->email,
                    'Name' => $user->first_name . " " . $user->last_name,
                ));
            }

            $this->editSubscriber($user->email, array(
                'Name' => $user->first_name . " " . $user->last_name,
                "CustomFields" => array(
                    array(
                        "Key" => "NumberofSites",
                        "Value" => $user->sites()->count()
                    ),
                    array(
                        "Key" => "SubscriptionPlan",
                        "Value" => $subscription != false ? $subscription->product()->name : null,
                    ),
                    array(
                        "Key" => "NumberofProducts",
                        "Value" => $subscription != false ? $user->products()->count() : null,
                    ),
                    array(
                        "Key" => "NumberofCategories",
                        "Value" => $user->categories()->count()
                    ),
                    array(
                        "Key" => "SubscribedDate",
                        "Value" => $subscription != false ? $subscription->created_at : null,
                    ),
                    array(
                        "Key" => "LastSubscriptionUpdatedDate",
                        "Value" => $subscription != false ? $subscription->updated_at : null,
                    ),
                    array(
                        "Key" => "TrialExpiry",
                        "Value" => $subscription != false ? $subscription->trial_ended_at : null,
                    ),
                    array(
                        "Key" => "SubscriptionCancelledDate",
                        "Value" => $subscription != false ? $subscription->canceled_at : null,
                    ),
                    array(
                        "Key" => "MaximumNumberofProducts",
                        "Value" => isset($criteria) && isset($criteria->product) && $criteria->product != 0 ? $criteria->product : null
                    ),
                    array(
                        "Key" => "MaximumNumberofSites",
                        "Value" => isset($criteria) && isset($criteria->site) && $criteria->site != 0 ? $criteria->site : null
                    ),
                    array(
                        "Key" => "LastLoginDate",
                        "Value" => date('Y/m/d', strtotime($user->last_login))
                    ),
                )
            ));
        }
    }

    public function syncAllUsersSubscriptions()
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->syncUserSubscription($user);
        }
    }

    public function updateLastConfiguredDashboardDate()
    {
        $user = auth()->user();
        if (!$user->needSubscription) {
            return true;
        }
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastConfiguredDashboardDate",
                    "Value" => date("Y/m/d")
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateNextLevelSubscriptionPlan(User $user)
    {
        if ($user->needSubscription && !is_null($user->subscription) && !is_null($user->apiSubscription)) {
            $subscription = $user->apiSubscription;

            /*somehow unable to use dependency injection in this repo*/
            /*fetch product list manually*/
            $products = Chargify::product()->all();
            $products = collect($products);
            $products = $products->filter(function ($product, $key) {
                return strpos(strtolower($product->name), 'onboarding') === false;
            });
            $products = $products->sortBy("price_in_cents");
            $nextProduct = null;
            $criteria = null;
            $items = "";
            foreach ($products as $key => $product) {
                if ($product->id == $subscription->product_id) {
                    if (isset($products[$key + 1])) {
                        $nextProduct = $products[$key + 1];
                        $criteria = json_decode($nextProduct->description);
                        if (isset($criteria->product)) {
                            if ($criteria->product != 0) {
                                $items .= "Up to " . $criteria->product . " Products. ";
                            } else {
                                $items .= "Unlimited Products. ";
                            }
                        }
                        if (isset($criteria->site)) {
                            if ($criteria->site != 0) {
                                $items .= "Up to " . $criteria->site . " " . str_plural("Competitor", $criteria->site) . ". ";
                            } else {
                                $items .= "Unlimited Competitor Tracking. ";
                            }
                        }
                        $items .= "Customisable Dashboard. ";
                        if (isset($criteria->alert_report)) {
                            if ($criteria->alert_report != 'basic') {
                                $items .= "Basic Alerts and Reports. ";
                            } else {
                                $items .= "Unlimited Alerts and Reports. ";
                            }
                        }
                        if (isset($criteria->frequency)) {
                            if ($criteria->frequency == 24) {
                                $items .= "Updates Event Day. ";
                            } else {
                                $items .= "Every " . $criteria->frequency . " " . str_plural('Hour', $criteria->frequency) . ". ";
                            }
                        }
                        if (isset($criteria->historic_pricing)) {
                            if ($criteria->historic_pricing == 0) {
                                $items .= "Lifetime Historic Pricing. ";
                            } else {
                                $items .= $criteria->historic_pricing . " " . str_plural('Month', $criteria->historic_pricing) . " Historic Pricing. ";
                            }
                        }
                        if (isset($criteria->my_price) && $criteria->my_price == true) {
                            $items .= "'My Price' Nomination. ";
                        }
                    }
                    break;
                }
            }

            $subscriber = $this->getSubscriber($user->email);
            if (is_null($subscriber)) {
                $this->addSubscriber(array(
                    'EmailAddress' => $user->email,
                    'Name' => $user->first_name . " " . $user->last_name,
                ));
            }

            $this->editSubscriber($user->email, array(
                "CustomFields" => array(
                    array(
                        "Key" => "NextLevelSubscriptionPlan",
                        "Value" => is_null($nextProduct) ? null : $nextProduct->name,
                    ),
                    array(
                        "Key" => "NextLevelMaximumNumberofSites",
                        "Value" => (is_null($criteria) || $criteria->site == 0) ? null : $criteria->site,
                    ),
                    array(
                        "Key" => "NextLevelMaximumNumberofProducts",
                        "Value" => (is_null($criteria) || $criteria->product == 0) ? null : $criteria->product,
                    ),
                    array(
                        "Key" => "NextLevelSubscriptionPlanDescription",
                        "Value" => $items,
                    ),
                )
            ));
        }

    }

    public function sendResetPasswordEmail(User $user, $token)
    {
        $smart_email_id = config('campaign_monitor.reset_password_email_id');
        $wrap = new \CS_REST_Transactional_SmartEmail($smart_email_id, $this->auth);
        $message = array(
            "To" => "{$user->first_name} <{$user->email}>",
            "Data" => array(
                'x-apple-data-detectors' => 'x-apple-data-detectorsTestValue',
                'href^="tel"' => 'href^="tel"TestValue',
                'href^="sms"' => 'href^="sms"TestValue',
                'owa' => 'owaTestValue',
                'firstname' => $user->first_name,
                'email' => $user->email,
                'reset_link' => route('password.reset.get', $token) . "?email={$user->email}",
            ),
        );
        $result = $wrap->send($message);
    }
}