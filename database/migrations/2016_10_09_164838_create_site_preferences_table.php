<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_preferences', function (Blueprint $table) {
            $table->increments('site_preference_id');
            $table->integer('site_id')->unsigned();
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('xpath_1')->nullable();
            $table->text('xpath_2')->nullable();
            $table->text('xpath_3')->nullable();
            $table->text('xpath_4')->nullable();
            $table->text('xpath_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_preferences');
    }
}
