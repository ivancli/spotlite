<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/4/2016
 * Time: 10:30 AM
 */

namespace App\Jobs;


use App\Contracts\Repository\Logger\AlertActivityLoggerContract;
use App\Models\Alert;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogAlertActivity
{
    use InteractsWithQueue, SerializesModels;

    protected $alert;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @param Alert $alert
     * @param $options
     */
    public function __construct(Alert $alert, $options)
    {
        $this->options = $options;
        $this->alert = $alert;
    }

    /**
     * Execute the job.
     * @param AlertActivityLoggerContract $alertActivityLoggerContract
     */
    public function handle(AlertActivityLoggerContract $alertActivityLoggerContract)
    {
        $alertActivityLoggerContract->storeLog($this->options, $this->alert);
    }
}