<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_preferences', function (Blueprint $table) {
            $table->increments('domain_preference_id');
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('domain_id')->on('domains')
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
        Schema::drop('domain_preferences');
    }
}
