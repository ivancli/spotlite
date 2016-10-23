<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InitialSeeder extends Seeder
{

    public function run()
    {
        $userId = DB::table('users')->insertGetId([
            'email' => 'ivan.li@invigorgroup.com',
            'password' => bcrypt('secret'),
        ]);

        $customerId = DB::table('users')->insertGetId([
            'email' => 'ivan.invigor@gmail.com',
            'password' => bcrypt('helloworld'),
        ]);

        $superAdminRole = \Invigor\UM\UMRole::where('name', 'super_admin')->first();
        $customerRole = \Invigor\UM\UMRole::where('name', 'client')->first();

        DB::table('role_user')->insert([
            'user_id' => $userId,
            'role_id' => $superAdminRole->role_id,
        ]);
        DB::table('role_user')->insert([
            'user_id' => $customerId,
            'role_id' => $customerRole->role_id,
        ]);



        $user = \App\Models\User::findOrFail($userId);
        $customer = \App\Models\User::findOrFail($customerId);

        \App\Models\UserPreference::setPreference($user, 'DATE_FORMAT', 'Y-m-d');
        \App\Models\UserPreference::setPreference($user, 'TIME_FORMAT', 'g:i a');

        \App\Models\UserPreference::setPreference($customer, 'DATE_FORMAT', 'Y-m-d');
        \App\Models\UserPreference::setPreference($customer, 'TIME_FORMAT', 'g:i a');



        /* CRAWLING CHECKPOINT */
        DB::table("app_preferences")->insert([
            "element" => "CRAWL_TIME",
            "value" => "0,2,4,6,8,10,12,14,16,18,20,22"
        ]);

        DB::table("app_preferences")->insert([
            "element" => "CRAWL_RESERVED",
            "value" => "n",
        ]);
        DB::table("app_preferences")->insert([
            "element" => "CRAWL_RESERVED_BY",
            "value" => null,
        ]);
        DB::table("app_preferences")->insert([
            "element" => "CRAWL_LAST_RESERVED_AT",
            "value" => null,
        ]);


        /* SYNC USER CHECKPOINT*/

        DB::table("app_preferences")->insert([
            "element" => "SYNC_TIME",
            "value" => "0,4,8,12,16,20",
        ]);

        DB::table("app_preferences")->insert([
            "element" => "SYNC_RESERVED",
            "value" => "n",
        ]);
        DB::table("app_preferences")->insert([
            "element" => "SYNC_RESERVED_BY",
            "value" => null,
        ]);
        DB::table("app_preferences")->insert([
            "element" => "SYNC_LAST_RESERVED_AT",
            "value" => null,
        ]);


        /* REPORT CHECKPOINT*/

        DB::table("app_preferences")->insert([
            "element" => "REPORT_RESERVED",
            "value" => "n",
        ]);
        DB::table("app_preferences")->insert([
            "element" => "REPORT_RESERVED_BY",
            "value" => null,
        ]);
        DB::table("app_preferences")->insert([
            "element" => "REPORT_LAST_RESERVED_AT",
            "value" => null,
        ]);
    }
}