<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertActivityLogsTable extends Migration {

	public function up()
	{
		Schema::create('alert_activity_logs', function(Blueprint $table) {
			$table->bigIncrements('alert_activity_log_id');
            $table->integer('alert_id')->unsigned()->nullable();
            $table->text('alert_activity_log_owner_type');
            $table->integer('alert_activity_log_owner_id')->unsigned();
            $table->enum('type', array("trigger", "sent"));
            $table->text('content');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('alert_activity_logs');
	}
}