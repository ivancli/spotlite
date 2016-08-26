<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpsTable extends Migration {

	public function up()
	{
		Schema::create('ips', function(Blueprint $table) {
			$table->increments('ip_id');
			$table->string('ip_address', 45)->unique();
		});
	}

	public function down()
	{
		Schema::drop('ips');
	}
}