<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeletedSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deleted_sites', function (Blueprint $table) {
            $table->dropColumn('content');
            $table->dropColumn('deleted_site_id');
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
            $table->text('content');
            $table->increments('deleted_site_id');
        });
    }
}
