<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(SpotLiteRoleSeeder::class);
        $this->call(InitialSeeder::class);
        $this->call(DashboardSeeder::class);
        $this->call(SampleUserSeeder::class);
    }
}
