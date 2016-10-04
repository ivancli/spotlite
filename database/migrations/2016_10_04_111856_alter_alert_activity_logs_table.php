<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alert_activity_logs', function (Blueprint $table) {
            $table->enum('type', array("trigger", "sent"))->after("alert_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alert_activity_logs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
