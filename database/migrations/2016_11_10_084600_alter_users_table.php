<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->text('industry')->nullable();
            $table->text('company_type')->nullable();
            $table->text('company_name')->nullable();
            $table->text('company_url')->nullable();
            $table->text('ebay_username')->nullable();
            $table->char('agree_terms', 1)->default('y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('phone')->nullabe();
            $table->dropColumn('industry');
            $table->dropColumn('company_type');
            $table->dropColumn('company_name');
            $table->dropColumn('agree_terms');
        });
    }
}
