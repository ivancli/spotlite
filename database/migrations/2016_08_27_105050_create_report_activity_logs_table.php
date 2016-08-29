<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportActivityLogsTable extends Migration {

	public function up()
	{
		Schema::create('report_activity_logs', function(Blueprint $table) {
			$table->bigIncrements('report_activity_log_id');
			$table->integer('report_task_id')->unsigned()->index();
			$table->enum('status', array('started', 'prepared', 'validated', 'generated', 'saved'));
			$table->text('message')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('report_activity_logs');
	}
}