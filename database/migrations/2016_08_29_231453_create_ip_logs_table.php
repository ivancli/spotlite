<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_logs', function(Blueprint $table) {
            $table->bigIncrements('ip_log_id');
            $table->integer('ip_id')->unsigned()->index();
            $table->foreign('ip_id')->references('ip_id')->on('ips')
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
        Schema::drop('ip_logs');
    }
}
