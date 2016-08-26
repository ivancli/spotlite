<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCookiesTable extends Migration {

	public function up()
	{
		Schema::create('cookies', function(Blueprint $table) {
			$table->increments('cookie_id');
			$table->text('cookie_name');
			$table->text('cookie_header');
			$table->binary('cookie_file');
		});
	}

	public function down()
	{
		Schema::drop('cookies');
	}
}