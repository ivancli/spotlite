<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_templates', function (Blueprint $table) {
            $table->increments('dashboard_template_id');
            $table->text('dashboard_template_name');
            $table->text('dashboard_template_display_name');
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
        Schema::drop('dashboard_templates');
    }
}
