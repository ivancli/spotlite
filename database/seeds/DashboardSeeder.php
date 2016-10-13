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
        DB::table("dashboard_templates")->insert([
            "dashboard_template_name" => "default",
            "dashboard_template_display_name" => "Default Template",
            "is_hidden" => "n"
        ]);
    }
}