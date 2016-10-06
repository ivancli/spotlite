<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductSitesTable extends Migration {

	public function up()
	{
		Schema::create('product_sites', function(Blueprint $table) {
			$table->increments('product_site_id');
            $table->integer('product_id')->index()->unsigned();
            $table->integer('site_id')->index()->unsigned();
            $table->char('my_price', 1)->default('n')->comment('y=yes,n=no');
		});
	}

	public function down()
	{
		Schema::drop('product_sites');
	}
}