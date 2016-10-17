<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_preferences', function (Blueprint $table) {
            $table->increments('dashboard_preference_id');
            $table->integer('dashboard_id')->unsigned();
            $table->foreign('dashboard_id')->references('dashboard_id')->on('dashboards')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('element');
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dashboard_preferences');
    }
}
