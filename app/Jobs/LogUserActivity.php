<?php

namespace App\Jobs;

//use App\Contracts\LogManagement\Logger;
use App\Contracts\LogManagement\Logger;
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
     * @param Logger $logger
     */
    public function handle(Logger $logger)
    {
        $logger->storeLog($this->activity, $this->user);
    }
}
