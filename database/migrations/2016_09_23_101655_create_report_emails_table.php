<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_emails', function (Blueprint $table) {
            $table->increments('report_email_id');
            $table->integer('report_task_id')->unsigned();
            $table->foreign('report_task_id')->references('report_task_id')->on('report_tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('report_email_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_emails');
    }
}
