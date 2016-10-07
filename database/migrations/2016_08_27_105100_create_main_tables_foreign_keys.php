<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateMainTablesForeignKeys extends Migration
{

    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('category_id')->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
//        Schema::table('sites', function (Blueprint $table) {
//            $table->foreign('product_id')->references('product_id')->on('products')
//                ->onDelete('cascade')
//                ->onUpdate('cascade');
//        });
        Schema::table('product_sites', function (Blueprint $table) {
            $table->foreign('product_id')->references('product_id')->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('product_sites', function (Blueprint $table) {
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('historical_prices', function (Blueprint $table) {
            $table->foreign('crawler_id')->references('crawler_id')->on('crawlers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('historical_prices', function (Blueprint $table) {
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('crawlers', function (Blueprint $table) {
            $table->foreign('site_id')->references('site_id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('crawlers', function (Blueprint $table) {
            $table->foreign('cookie_id')->references('cookie_id')->on('cookies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('crawler_ips', function (Blueprint $table) {
            $table->foreign('crawler_id')->references('crawler_id')->on('crawlers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('crawler_ips', function (Blueprint $table) {
            $table->foreign('ip_id')->references('ip_id')->on('ips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('crawler_activity_logs', function (Blueprint $table) {
            $table->foreign('crawler_id')->references('crawler_id')->on('crawlers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('group_id')->references('group_id')->on('groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('report_task_id')->references('report_task_id')->on('report_tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('report_task_id')->references('report_task_id')->on('report_tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('report_task_id')->references('report_task_id')->on('report_tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('report_activity_logs', function (Blueprint $table) {
            $table->foreign('report_task_id')->references('report_task_id')->on('report_tasks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('user_id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->foreign('cookie_id')->references('cookie_id')->on('cookies')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('domain_ips', function (Blueprint $table) {
            $table->foreign('domain_id')->references('domain_id')->on('domains')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('domain_ips', function (Blueprint $table) {
            $table->foreign('ip_id')->references('ip_id')->on('ips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('alerts', function (Blueprint $table) {
            $table->foreign('comparison_product_site_id')->references('product_site_id')->on('product_sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('alert_emails', function (Blueprint $table) {
            $table->foreign('alert_id')->references('alert_id')->on('alerts')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('alert_activity_logs', function (Blueprint $table) {
            $table->foreign('alert_id')->references('alert_id')->on('alerts')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_id_foreign');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_user_id_foreign');
        });
//        Schema::table('sites', function (Blueprint $table) {
//            $table->dropForeign('sites_product_id_foreign');
//        });
        Schema::table('product_sites', function (Blueprint $table) {
            $table->dropForeign('product_sites_product_id_foreign');
        });
        Schema::table('product_sites', function (Blueprint $table) {
            $table->dropForeign('product_sites_site_id_foreign');
        });
        Schema::table('historical_prices', function (Blueprint $table) {
            $table->dropForeign('historical_prices_crawler_id_foreign');
        });
        Schema::table('historical_prices', function (Blueprint $table) {
            $table->dropForeign('historical_prices_site_id_foreign');
        });
        Schema::table('crawlers', function (Blueprint $table) {
            $table->dropForeign('crawlers_site_id_foreign');
        });
        Schema::table('crawlers', function (Blueprint $table) {
            $table->dropForeign('crawlers_cookie_id_foreign');
        });
        Schema::table('crawler_ips', function (Blueprint $table) {
            $table->dropForeign('crawler_ips_crawler_id_foreign');
        });
        Schema::table('crawler_ips', function (Blueprint $table) {
            $table->dropForeign('crawler_ips_ip_id_foreign');
        });
        Schema::table('crawler_activity_logs', function (Blueprint $table) {
            $table->dropForeign('crawler_activity_logs_crawler_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_user_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_group_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_report_task_id_foreign');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_report_task_id_foreign');
        });
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_report_task_id_foreign');
        });
        Schema::table('report_activity_logs', function (Blueprint $table) {
            $table->dropForeign('report_activity_logs_report_task_id_foreign');
        });
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropForeign('user_activity_logs_user_id_foreign');
        });
        Schema::table('domains', function (Blueprint $table) {
            $table->dropForeign('domains_cookie_id_foreign');
        });
        Schema::table('domain_ips', function (Blueprint $table) {
            $table->dropForeign('domain_ips_domain_id_foreign');
        });
        Schema::table('domain_ips', function (Blueprint $table) {
            $table->dropForeign('domain_ips_ip_id_foreign');
        });
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign('alerts_comparison_product_site_id_foreign');
        });
        Schema::table('alert_emails', function (Blueprint $table) {
            $table->dropForeign('alert_emails_alert_id_foreign');
        });
        Schema::table('alert_activity_logs', function (Blueprint $table) {
            $table->dropForeign('alert_activity_logs_alert_id_foreign');
        });
    }
}