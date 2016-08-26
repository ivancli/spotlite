<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserActivityLogsTable extends Migration {

	public function up()
	{
		Schema::create('user_activity_logs', function(Blueprint $table) {
			$table->bigIncrements('user_activity_log_id');
			$table->integer('user_id')->unsigned()->index();
			$table->text('activity');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('user_activity_logs');
	}
}