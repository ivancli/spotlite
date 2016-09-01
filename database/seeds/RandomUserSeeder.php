<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RandomUserSeeder extends Seeder
{

    public function run()
    {
        factory(App\Models\User::class, 123)->create();
    }
}