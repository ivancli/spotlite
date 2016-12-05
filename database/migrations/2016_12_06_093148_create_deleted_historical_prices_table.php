<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletedHistoricalPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_historical_prices', function (Blueprint $table) {
            $table->bigInteger('deleted_historical_price_id');
            $table->primary('deleted_historical_price_id');
            $table->integer('crawler_id')->unsigned()->nullable();
            $table->integer('site_id')->unsigned()->nullable();
            $table->decimal('price', 20, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('deleted_historical_prices');
    }
}
