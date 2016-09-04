<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_email_logs', function(Blueprint $table) {
            $table->bigIncrements('alert_email_log_id');
            $table->integer('alert_email_id')->unsigned()->index();
            $table->foreign('alert_email_id')->references('alert_email_id')->on('alert_emails')
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
        Schema::drop('alert_email_logs');
    }
}
