<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/16/2016
 * Time: 11:23 AM
 */

namespace App\Jobs;


use App\Contracts\SubscriptionManagement\SubscriptionManager;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     * @param SubscriptionManager $subscriptionManager
     */
    public function handle(SubscriptionManager $subscriptionManager)
    {
        $subscriptionManager->syncUserSubscription($this->user);
    }
}