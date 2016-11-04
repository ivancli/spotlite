<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/4/2016
 * Time: 2:17 PM
 */

namespace App\Contracts\Repository\Mailer;


interface MailingAgentContract
{
    public function getSubscriber($listId, $email);

    public function addSubscriber($listId, $fields);

    public function editSubscriber($listId, $email, $fields);

    public function deleteSubscriber($listId, $email);

    public function unsubscribe($listId, $email);
}