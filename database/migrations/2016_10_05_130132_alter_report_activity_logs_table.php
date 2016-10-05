<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_activity_logs', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('type', array("trigger", "sent", "create"))->after("report_task_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_activity_logs', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->enum('status', array('started', 'prepared', 'validated', 'generated', 'saved'))->after("report_task_id");
        });
    }
}
