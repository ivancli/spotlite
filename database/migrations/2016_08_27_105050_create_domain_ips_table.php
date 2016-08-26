<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDomainIpsTable extends Migration {

	public function up()
	{
		Schema::create('domain_ips', function(Blueprint $table) {
			$table->increments('domain_ip_id');
			$table->integer('domain_id')->unsigned()->index();
			$table->integer('ip_id')->unsigned()->index();
		});
	}

	public function down()
	{
		Schema::drop('domain_ips');
	}
}