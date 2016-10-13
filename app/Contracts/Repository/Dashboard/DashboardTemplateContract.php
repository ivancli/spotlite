<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 1:06 PM
 */

namespace App\Contracts\Repository\Dashboard;


interface DashboardTemplateContract
{
    public function getTemplates($includeHidden = false);

    public function getTemplate($id, $fail = true);
}