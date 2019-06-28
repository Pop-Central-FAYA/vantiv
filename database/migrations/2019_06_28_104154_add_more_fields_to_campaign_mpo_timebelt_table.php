<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToCampaignMpoTimebeltTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_mpo_time_belts', function (Blueprint $table) {
            $table->date('playout_date');
            $table->string('asset_id')->reference('id')->on('media_assets')->nullable();
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
            $table->dropColumn(['playout_date', 'asset_id']);
        });
    }
}
