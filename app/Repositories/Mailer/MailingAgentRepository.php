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

    public function __construct()
    {
        new \CS_REST_Subscribers('');
    }

    public function getSubscriber($listId, $email)
    {
        // TODO: Implement getSubscriber() method.
    }

    public function addSubscriber($listId, $fields)
    {
        // TODO: Implement addSubscriber() method.
    }

    public function editSubscriber($listId, $email, $fields)
    {
        // TODO: Implement editSubscriber() method.
    }

    public function deleteSubscriber($listId, $email)
    {
        // TODO: Implement deleteSubscriber() method.
    }

    public function unsubscribe($listId, $email)
    {
        // TODO: Implement unsubscribe() method.
    }
}