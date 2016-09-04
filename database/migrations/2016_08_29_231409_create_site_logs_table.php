<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_logs', function(Blueprint $table) {
            $table->bigIncrements('site_log_id');
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->enum('type', array('c', 'u', 'd', 'r'))->comment = "c=create, u=update, d=delete, r=restore";
            $table->text('content');
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
        Schema::drop('site_logs');
    }
}
