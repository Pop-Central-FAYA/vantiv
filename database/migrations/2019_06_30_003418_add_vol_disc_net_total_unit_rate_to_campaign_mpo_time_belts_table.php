<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVolDiscNetTotalUnitRateToCampaignMpoTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_mpo_time_belts', function (Blueprint $table) {
            $table->integer('volume_discount');
            $table->bigInteger('net_total');
            $table->integer('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_mpo_time_belts', function (Blueprint $table) {
            $table->dropColumn(['volume_discount', 'net_total', 'unit_rate']);
        });
    }
}
