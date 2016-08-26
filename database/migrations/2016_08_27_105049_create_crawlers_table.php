<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrawlersTable extends Migration
{

    public function up()
    {
        Schema::create('crawlers', function (Blueprint $table) {
            $table->increments('crawler_id');
            $table->text('crawler_class')->nullable();
            $table->text('parser_class')->nullable();
            $table->enum('status', array('picked', 'queuing', 'running'))->nullable();
            $table->integer('site_id')->unsigned()->index();
            $table->integer('cookie_id')->unsigned()->nullable()->index();
            $table->timestamp('active_at')->nullable();
        });
    }

    public function down()
    {
        Schema::drop('crawlers');
    }
}