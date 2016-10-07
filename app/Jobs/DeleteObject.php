<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/7/2016
 * Time: 10:55 AM
 */

namespace App\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteObject extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $object;

    /**
     * Create a new job instance.
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->object->delete();
    }
}