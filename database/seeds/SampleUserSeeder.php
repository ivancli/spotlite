<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SampleUserSeeder extends Seeder
{

    public function run()
    {

        $sampleUserId = DB::table('users')->insertGetId([
            'title' => 'Mr.',
            'email' => 'admin@spotlite.com.au',
            'first_name' => 'Sample',
            'last_name' => 'User',
            'password' => bcrypt('S0lutions'),
        ]);

        $superAdminRole = \Invigor\UM\UMRole::where('name', 'super_admin')->first();

        DB::table('role_user')->insert([
            'user_id' => $sampleUserId,
            'role_id' => $superAdminRole->role_id,
        ]);

        $sampleUser = \App\Models\User::findOrFail($sampleUserId);

        \App\Models\UserPreference::setPreference($sampleUser, 'DATE_FORMAT', 'Y-m-d');
        \App\Models\UserPreference::setPreference($sampleUser, 'TIME_FORMAT', 'g:i a');


    }
}