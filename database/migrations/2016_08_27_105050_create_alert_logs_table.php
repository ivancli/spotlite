<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertLogsTable extends Migration {

	public function up()
	{
		Schema::create('alert_logs', function(Blueprint $table) {
			$table->bigIncrements('alert_log_id');
            $table->integer('alert_id')->unsigned();
            $table->text('content');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('alert_logs');
	}
}