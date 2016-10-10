<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertExcludedSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_excluded_sites', function (Blueprint $table) {
            $table->bigIncrements('alert_exclude_site_id');
            $table->integer('alert_id')->unsigned()->index();
            $table->foreign('alert_id')->references('alert_id')->on('alerts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("alert_excluded_sites");
    }
}
