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
            $table->enum('my_price', array('y', 'n'))->default('n');
		});
	}

	public function down()
	{
		Schema::drop('product_sites');
	}
}