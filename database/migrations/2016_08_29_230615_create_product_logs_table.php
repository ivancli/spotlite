<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_logs', function(Blueprint $table) {
            $table->bigIncrements('product_log_id');
            $table->integer('product_id')->unsigned()->index();
            $table->foreign('product_id')->references('product_id')->on('products')
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
        Schema::drop('product_logs');
    }
}
