<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SpotLiteRoleSeeder extends Seeder
{

    public function run()
    {
        $tier_1 = new \Invigor\UM\UMRole();
        $tier_1->name = "tier_1";
        $tier_1->display_name = "Tier 1 Admin";
        $tier_1->save();

        $tier_2 = new \Invigor\UM\UMRole();
        $tier_2->name = "tier_2";
        $tier_2->display_name = "Tier 2 Staff";
        $tier_2->save();

        $client = new \Invigor\UM\UMRole();
        $client->name = "client";
        $client->display_name = "Client";
        $client->save();
    }
}