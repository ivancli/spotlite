<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertActivityLogsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alert_activity_logs', function (Blueprint $table) {
            $table->text('alert_activity_log_owner_type')->after("alert_activity_log_id");
            $table->integer('alert_activity_log_owner_id')->after("alert_activity_log_id")->unsigned();
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
            $table->dropColumn('alert_activity_log_owner_type');
            $table->dropColumn('alert_activity_log_owner_id');
        });
    }
}
