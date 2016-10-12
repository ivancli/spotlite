<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->increments('dashboard_widget_id');
            $table->integer('dashboard_id')->unsigned();
            $table->integer('dashboard_widget_type_id')->unsigned();
            $table->text('dashboard_widget_name');
            $table->integer('dashboard_widget_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dashboard_widgets');
    }
}
