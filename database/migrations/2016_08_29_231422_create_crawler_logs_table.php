<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrawlerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_logs', function(Blueprint $table) {
            $table->bigIncrements('crawler_log_id');
            $table->integer('crawler_id')->unsigned()->index();
            $table->foreign('crawler_id')->references('crawler_id')->on('crawlers')
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
        Schema::drop('crawler_logs');
    }
}
