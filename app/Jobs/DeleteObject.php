<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/7/2016
 * Time: 10:55 AM
 */

namespace App\Jobs;


use App\Contracts\Repository\Product\Alert\AlertContract;
use DaveJamesMiller\Breadcrumbs\Exception;
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
     * @param AlertContract $alertRepo
     */
    public function handle(AlertContract $alertRepo)
    {
        try {
            $this->object->delete();
        } catch (Exception $e) {
            //object has been deleted before hand
            /*TODO need a better way to handle this*/
        }
    }
}