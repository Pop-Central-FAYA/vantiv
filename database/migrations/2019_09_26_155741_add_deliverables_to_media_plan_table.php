<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliverablesToMediaPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->integer('target_population');
            $table->integer('population');
            $table->integer('gross_impressions');
            $table->integer('total_insertions');
            $table->integer('net_reach');

            $table->decimal('net_media_cost', 15, 2);
            $table->decimal('cpm', 8, 2);
            $table->decimal('cpp', 8, 2);
            $table->decimal('avg_frequency', 8, 2);
            $table->decimal('total_grp', 8, 2);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_plans', function (Blueprint $table) {
            $table->dropColumn('target_population');
            $table->dropColumn('population');
            $table->dropColumn('total_insertions');
            $table->dropColumn('gross_impressions');
            $table->dropColumn('total_grp');
            $table->dropColumn('net_reach');
            $table->dropColumn('avg_frequency');
            $table->dropColumn('net_media_cost');
            $table->dropColumn('cpm');
            $table->dropColumn('cpp');
        });
    }
}        