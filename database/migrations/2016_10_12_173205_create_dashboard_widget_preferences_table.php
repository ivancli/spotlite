<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardWidgetPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_widget_preferences', function (Blueprint $table) {
            $table->increments('dashboard_widget_preference_id');
            $table->integer('dashboard_widget_id')->unsigned();
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
        Schema::drop('dashboard_widget_preferences');
    }
}
