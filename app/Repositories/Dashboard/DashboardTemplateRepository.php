<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 1:08 PM
 */

namespace App\Repositories\Dashboard;


use App\Contracts\Repository\Dashboard\DashboardTemplateContract;
use App\Models\Dashboard\DashboardTemplate;
use Illuminate\Http\Request;

class DashboardTemplateRepository implements DashboardTemplateContract
{
    protected $dashboardTemplate;
    protected $request;

    public function __construct(DashboardTemplate $dashboardTemplate, Request $request)
    {
        $this->dashboardTemplate = $dashboardTemplate;
        $this->request = $request;
    }

    public function getTemplates($includeHidden = false)
    {
        if ($includeHidden) {
            return $this->dashboardTemplate->all();
        } else {
            return $this->dashboardTemplate->where("is_hidden", null)->orWhere("is_hidden", 'n')->get();
        }
    }

    public function getTemplate($id, $fail = true)
    {
        if ($fail) {
            return $this->dashboardTemplate->findOrFail($id);
        } else {
            return $this->dashboardTemplate->find($id);
        }
    }
}