<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration {

	public function up()
	{
		Schema::create('reports', function(Blueprint $table) {
			$table->bigIncrements('report_id');
			$table->integer('report_task_id')->unsigned()->index();
//			$table->binary('content')->nullable();
		});
		DB::statement("ALTER TABLE reports ADD content MEDIUMBLOB NOT NULL AFTER report_task_id");
	}

	public function down()
	{
		Schema::drop('reports');
	}
}