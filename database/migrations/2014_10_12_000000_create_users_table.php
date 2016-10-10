<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->text('title')->nullabe();
            $table->text('first_name');
            $table->text('last_name');
            $table->string('email')->unique();
            $table->text('phone')->nullabe();
            $table->string('password');
            $table->text('verification_code');
            $table->char('is_first_login', 1)->nullable()->comment("y=yes,n=no");
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
