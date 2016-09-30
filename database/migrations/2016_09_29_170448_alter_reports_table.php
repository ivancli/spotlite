<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('report_owner_type', array('product', 'category'))->after("report_id")->comment("product report or category report");
            $table->integer('report_owner_id')->unsigned()->after("report_owner_type")->comment("product_id or category_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('report_owner_type');
            $table->dropColumn('report_owner_id');
        });
    }
}
