<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeletedSitesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deleted_sites', function (Blueprint $table) {
            $table->integer('deleted_site_id');
            $table->primary('deleted_site_id');
            $table->string("site_url", 2083);
            $table->char('my_price', 1)->nullable();
            $table->text('status')->nullable();
            $table->decimal('recent_price', 20, 4)->nullable();
            $table->decimal('price_diff', 20, 4)->nullable();
            $table->timestamp('last_crawled_at')->nullable();
            $table->text('comment')->nullable();
            $table->integer('site_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deleted_sites', function (Blueprint $table) {
            $table->dropColumn('deleted_site_id');
            $table->dropColumn("site_url");
            $table->dropColumn('my_price');
            $table->dropColumn('status');
            $table->dropColumn('recent_price');
            $table->dropColumn('price_diff');
            $table->dropColumn('last_crawled_at');
            $table->dropColumn('comment');
            $table->dropColumn('site_order');
        });
    }
}
