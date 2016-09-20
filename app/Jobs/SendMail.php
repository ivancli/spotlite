<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 20/09/2016
 * Time: 10:05 PM
 */

namespace App\Jobs;


use App\Contracts\EmailManagement\EmailGenerator;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    protected $view;
    protected $data;
    protected $subject;

    /**
     * Create a new job instance.
     * @param $view
     * @param array $data
     * @param User $user
     * @param $subject
     */
    public function __construct($view, array $data = array(), User $user, $subject)
    {
        $this->user = $user;
        $this->view = $view;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @param EmailGenerator $emailGenerator
     */
    public function handle(EmailGenerator $emailGenerator)
    {
        $emailGenerator->sendMail($this->view, $this->data, $this->user, $this->subject);
    }
}
