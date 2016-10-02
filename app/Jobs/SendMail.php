<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 20/09/2016
 * Time: 10:05 PM
 */

namespace App\Jobs;


use App\Contracts\Repository\Mailer\MailerContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $options;
    protected $view;
    protected $data;

    /**
     * Create a new job instance.
     * @param $view
     * @param array $data
     * @param array $options
     */
    public function __construct($view, array $data = array(), array $options = array())
    {
        $this->view = $view;
        $this->data = $data;

        /*
         * options include:
             * email
             * first_name
             * last_name
             * subject
             * optional attachment
             * attachment data needs to be in base64 encoded, since data would be transferred through in database queue.
         * */
        $this->options = $options;
    }

    /**
     * Execute the job.
     * @param MailerContract $mailer
     */
    public function handle(MailerContract $mailer)
    {
        $mailer->sendMail($this->view, $this->data, $this->options);
    }
}
