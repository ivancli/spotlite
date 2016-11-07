<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/4/2016
 * Time: 2:17 PM
 */

namespace App\Repositories\Mailer;


use App\Contracts\Repository\Mailer\MailingAgentContract;

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
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "NumberofCategories",
                    "Value" => $user->categories()->count()
                )
            )
        ));
        if ($result->http_status_code == 200) {
            return $result;
        } else {
            return $result->response;
        }
    }

    public function updateLastAddCategoryDate()
    {
        $user = auth()->user();
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddCategoryOn",
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

    public function updateLastAddProductDate()
    {
        $user = auth()->user();
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddProductOn",
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
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastAddSiteOn",
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
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastNominatedMySiteOn",
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
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastSetupAlertOn",
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
        $result = $this->editSubscriber($user->email, array(
            "CustomFields" => array(
                array(
                    "Key" => "LastSetupReportOn",
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
}