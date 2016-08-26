<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrawlerIpsTable extends Migration {

	public function up()
	{
		Schema::create('crawler_ips', function(Blueprint $table) {
			$table->increments('crawler_ip_id');
			$table->integer('crawler_id')->unsigned()->index();
			$table->integer('ip_id')->unsigned()->index();
		});
	}

	public function down()
	{
		Schema::drop('crawler_ips');
	}
}