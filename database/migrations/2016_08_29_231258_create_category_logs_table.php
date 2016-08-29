<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_logs', function(Blueprint $table) {
            $table->bigIncrements('category_log_id');
            $table->integer('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('category_id')->on('categories')
                ->onDelete('no action')
                ->onUpdate('no action');
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
        Schema::drop('category_logs');
    }
}
