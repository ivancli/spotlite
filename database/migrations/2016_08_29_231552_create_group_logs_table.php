<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_logs', function(Blueprint $table) {
            $table->bigIncrements('group_log_id');
            $table->integer('group_id')->unsigned()->index();
            $table->foreign('group_id')->references('group_id')->on('groups')
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
        Schema::drop('group_logs');
    }
}
