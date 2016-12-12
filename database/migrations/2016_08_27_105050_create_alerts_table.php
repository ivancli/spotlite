<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertsTable extends Migration {

	public function up()
	{
		Schema::create('alerts', function(Blueprint $table) {
			$table->increments('alert_id');
            $table->integer('alert_owner_id')->unsigned();
            $table->enum('alert_owner_type', array('category', 'product', 'site', 'user'));
            $table->enum('comparison_price_type', array('specific price', 'my price', 'price changed'));
            $table->decimal('comparison_price', 20, 4)->nullable();
            $table->integer('comparison_site_id')->unsigned()->nullable();
            $table->enum('operator', array('=<', '<', '=>', '>'))->nullable();
            $table->char('one_off', 1)->default('n')->comment("y=yes,n=no");
            $table->timestamp('last_active_at')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('alerts');
	}
}