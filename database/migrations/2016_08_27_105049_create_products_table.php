<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('product_id');
			$table->text('product_name');
			$table->integer('category_id')->unsigned()->nullable()->index();
			$table->integer('user_id')->unsigned()->index()->nullable();
            $table->integer('group_id')->unsigned()->index()->nullable();
            $table->integer('product_order')->unsigned()->nullable();
			$table->integer('report_task_id')->unsigned()->index()->nullable();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}