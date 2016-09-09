<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/9/2016
 * Time: 9:15 AM
 */

namespace App\Repositories\ProductManagement;


use App\Contracts\ProductManagement\AlertManager;
use App\Models\Alert;

class SLAlertManager implements AlertManager
{

    public function getAlerts()
    {
        $alerts = Alert::all();
        return $alerts;
    }

    public function getAlert($alert_id)
    {
        $alert = Alert::findOrFail($alert_id);
        return $alert;
    }

    public function storeAlert($options)
    {
        $alert = Alert::create($options);
        return $alert;
    }

    public function updateAlert($alert_id, $options)
    {
        $alert = $this->getAlert($alert_id);
        $alert->update($options);
        return $alert;
    }

    public function deleteAlert($alert_id)
    {
        $alert = $this->getAlert($alert_id);
        $alert->delete();
    }
}