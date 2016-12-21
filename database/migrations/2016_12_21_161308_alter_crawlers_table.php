<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCrawlersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crawlers', function (Blueprint $table) {
            $table->text('currency_formatter_class', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crawlers', function (Blueprint $table) {
            $table->dropColumn('currency_formatter_class', 1);
        });
    }
}
