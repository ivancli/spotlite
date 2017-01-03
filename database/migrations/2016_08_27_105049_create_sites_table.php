<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSitesTable extends Migration
{

    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('site_id');
            $table->integer('product_id')->unsigned()->nullable();
            $table->string('site_url', 2083)->nullable()->index();
            $table->char('my_price', 1)->nullable()->comment("y=yes,n=no");
            $table->enum('status', array(
                "ok",
                "fail_html",
                "fail_price",
                "fail_xpath",
                "null_xpath",
                "waiting",
                "invalid",
                "no_price",
                "sample",
            ))->default("waiting");
            $table->decimal('recent_price', 20, 4)->nullable();
            $table->decimal('price_diff', 20, 4)->nullable();
            $table->timestamp('last_crawled_at')->nullable();
            $table->text('comment')->nullable();
            $table->integer('site_order')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('sites');
    }
}