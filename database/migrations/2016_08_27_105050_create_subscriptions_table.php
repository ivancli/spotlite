<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration {

	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $table) {
			$table->increments('subscription_id');
			$table->integer('user_id')->unsigned()->index();
			$table->text('api_product_id');
			$table->text('api_customer_id');
			$table->text('api_subscription_id');
			$table->timestamp('expiry_date');
		});
	}

	public function down()
	{
		Schema::drop('subscriptions');
	}
}