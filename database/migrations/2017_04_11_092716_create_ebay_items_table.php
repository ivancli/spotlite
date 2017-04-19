<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEbayItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ebay_items', function (Blueprint $table) {
            $table->increments('ebay_item_id');
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('shortDescription')->nullable();
            $table->string('price')->nullable();
            $table->string('currency')->nullable();
            $table->string('category')->nullable();
            $table->string('condition')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_postcode')->nullable();
            $table->string('location_country')->nullable();
            $table->string('image_url')->nullable();
            $table->string('brand')->nullable();
            $table->string('seller_username')->nullable();

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
        Schema::drop('ebay_items');
    }
}
