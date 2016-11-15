<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 11/15/2016
 * Time: 9:50 AM
 */

namespace App\Contracts\Repository\Subscription;


use App\Models\User;

interface OnboardingContract
{
    public function all();

    public function getByProductFamily($productFamilyId);

    public function previewSubscription($productId);

    public function storeSubscription($productId, User $user);

    public function migrateSubscription($productId, User $user);
}