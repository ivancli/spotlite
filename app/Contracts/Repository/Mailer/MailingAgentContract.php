<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/4/2016
 * Time: 2:17 PM
 */

namespace App\Contracts\Repository\Mailer;


use App\Models\User;

interface MailingAgentContract
{
    public function getSubscriber($email);

    public function addSubscriber($fields);

    public function editSubscriber($email, $fields);

    public function deleteSubscriber($email);

    public function unsubscribe($email);

    public function updateNumberOfSites();

    public function updateNumberOfProducts();

    public function updateNumberOfCategories();

    public function updateLastAddCategoryDate();

    public function updateLastAddProductDate();

    public function updateLastAddSiteDate();

    public function updateLastNominatedMyPriceDate();

    public function updateLastSetupAlertDate();

    public function updateLastSetupReportDate();

    public function updateLastConfiguredDashboardDate();

    public function updateNextLevelSubscriptionPlan(User $user);

    public function syncUserSubscription(User $user);

    public function syncAllUsersSubscriptions();
}