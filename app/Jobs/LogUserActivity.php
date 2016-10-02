<?php

namespace App\Jobs;

use App\Contracts\Repository\Logger\UserActivityLoggerContract;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserActivity extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $activity;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param $activity
     */
    public function __construct(User $user, $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    /**
     * Execute the job.
     * @param UserActivityLoggerContract $userActivityLogger
     */
    public function handle(UserActivityLoggerContract $userActivityLogger)
    {
        $userActivityLogger->storeLog($this->activity, $this->user);
    }
}
