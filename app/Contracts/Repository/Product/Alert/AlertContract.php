<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:06 PM
 */

namespace App\Contracts\Repository\Product\Alert;


use App\Models\Alert;

interface AlertContract
{
    public function getAlerts();

    public function getAlert($alert_id);

    public function storeAlert($options);

    public function updateAlert($alert_id, $options);

    public function deleteAlert($alert_id);

    public function triggerProductAlert(Alert $alert);

    public function triggerSiteAlert(Alert $alert);

    public function getDataTableAlerts();

    public function getProductAlertsByAuthUser();

    public function getSiteAlertsByAuthUser();
}