<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration
{

    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('report_id');
            $table->enum('report_owner_type', array('product', 'category'))->comment("product report or category report");
            $table->integer('report_owner_id')->unsigned()->comment("product_id or category_id");
            $table->integer('report_task_id')->unsigned()->nullable()->index();
            $table->text('file_name');
            $table->enum('file_type', array('xls', 'xlsx', 'csv', 'pdf'))->default('xlsx');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE reports ADD content MEDIUMBLOB NOT NULL AFTER report_task_id");
    }

    public function down()
    {
        Schema::drop('reports');
    }
}