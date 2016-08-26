<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	public function up()
	{
		Schema::create('categories', function(Blueprint $table) {
			$table->increments('category_id');
			$table->text('category_name');
			$table->integer('category_order')->unsigned()->nullable();
			$table->integer('report_task_id')->unsigned()->index()->nullable();
		});
	}

	public function down()
	{
		Schema::drop('categories');
	}
}