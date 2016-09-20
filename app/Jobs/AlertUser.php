<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/20/2016
 * Time: 5:34 PM
 */

namespace App\Jobs;


use App\Contracts\ProductManagement\AlertManager;
use App\Models\Alert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlertUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $alert;

    /**
     * Create a new job instance.
     * @param Alert $alert
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Execute the job.
     * @param AlertManager $alertManager
     */
    public function handle(AlertManager $alertManager)
    {
        switch ($this->alert->alert_owner_type) {
            case "product_site":
                $alertManager->triggerProductSiteAlert($this->alert);
                break;
            case "product":
                $alertManager->triggerProductAlert($this->alert);
                break;
            default:
        }
    }
}