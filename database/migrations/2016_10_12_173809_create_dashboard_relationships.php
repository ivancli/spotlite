<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dashboards', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('dashboards', function (Blueprint $table) {
            $table->foreign('dashboard_template_id')->references('dashboard_template_id')->on('dashboard_templates')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->foreign('dashboard_id')->references('dashboard_id')->on('dashboards')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->foreign('dashboard_widget_type_id')->references('dashboard_widget_type_id')->on('dashboard_widget_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('dashboard_widget_preferences', function (Blueprint $table) {
            $table->foreign('dashboard_widget_id')->references('dashboard_widget_id')->on('dashboard_widgets')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('dashboard_widget_types', function (Blueprint $table) {
            $table->foreign('dashboard_widget_template_id')->references('dashboard_widget_template_id')->on('dashboard_widget_templates')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('dashboards', function (Blueprint $table) {
            $table->dropForeign('dashboards_user_id_foreign');
        });
        Schema::table('dashboards', function (Blueprint $table) {
            $table->dropForeign('dashboards_dashboard_template_id_foreign');
        });
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->dropForeign('dashboard_widgets_dashboard_id_foreign');
        });
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->dropForeign('dashboard_widgets_dashboard_widget_type_id_foreign');
        });
        Schema::table('dashboard_widget_preferences', function (Blueprint $table) {
            $table->dropForeign('dashboard_widget_preferences_dashboard_widget_id_foreign');
        });
        Schema::table('dashboard_widget_types', function (Blueprint $table) {
            $table->dropForeign('dashboard_widget_types_dashboard_widget_template_id_foreign');
        });
    }
}
