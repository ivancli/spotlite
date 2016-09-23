<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportTasksTable extends Migration
{

    public function up()
    {
        Schema::create('report_tasks', function (Blueprint $table) {
            $table->increments('report_task_id');
            $table->enum('report_task_owner_type', array('product', 'category'));
            $table->integer('report_task_owner_id')->unsigned();
            $table->enum('frequency', array('daily', 'weekly', 'monthly'))->index();
            $table->smallInteger('date')->unsigned()->nullable()->index()->default('1');
            $table->tinyInteger('day')->unsigned()->nullable()->index();
            $table->time('time')->nullable();
            $table->enum('weekday_only', array('yes', 'no'))->nullable();
            $table->enum('file_type', array('xls', 'xlsx', 'csv', 'pdf'))->default('xlsx');
            $table->enum('delivery_method', array('email', 'sms'));
            $table->enum('status', array('picked', 'queuing', 'running'))->nullable();
            $table->timestamp('last_sent_at')->nullable();
        });
    }

    public function down()
    {
        Schema::drop('report_tasks');
    }
}