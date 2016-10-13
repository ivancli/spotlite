<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardWidgetTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_widget_templates', function (Blueprint $table) {
            $table->increments('dashboard_widget_template_id');
            $table->text('dashboard_widget_template_name');
            $table->char('is_hidden', 1)->default('n');
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
