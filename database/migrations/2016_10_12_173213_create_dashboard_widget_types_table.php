<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardWidgetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_widget_types', function (Blueprint $table) {
            $table->increments('dashboard_widget_type_id');
            $table->integer('dashboard_widget_template_id')->unsigned();
            $table->text('dashboard_widget_type_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dashboard_widget_types');
    }
}
