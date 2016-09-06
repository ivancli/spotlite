<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSitesTable extends Migration {

	public function up()
	{
		Schema::create('sites', function(Blueprint $table) {
			$table->increments('site_id');
			$table->string('site_url', 2083)->nullable()->index();
			$table->decimal('recent_price', 20, 4)->nullable();
			$table->timestamp('last_crawled_at')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('sites');
	}
}