<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 1:21 PM
 */
class DashboardSeeder extends Seeder
{

    public function run()
    {
        $dashboardTemplate = \App\Models\Dashboard\DashboardTemplate::create([
            "dashboard_template_name" => "default",
            "dashboard_template_display_name" => "Default Template",
            "is_hidden" => "n"
        ]);

        $dashboardWidgetTemplate = \App\Models\Dashboard\DashboardWidgetTemplate::create([
            "dashboard_widget_template_name" => "default_chart",
            "dashboard_widget_template_display_name" => "Default Chart Template"
        ]);

        $dashboardWidgetType = \App\Models\Dashboard\DashboardWidgetType::create([
            "dashboard_widget_template_id" => $dashboardWidgetTemplate->getKey(),
            "dashboard_widget_type_name" => "Chart"
        ]);
    }
}