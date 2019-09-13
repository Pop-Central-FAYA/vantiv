<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCampaignMpoTimeBeltsTableToCampaignTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('campaign_mpo_time_belts')){
            Schema::rename('campaign_mpo_time_belts', 'campaign_time_belts');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('campaign_time_belts')){
            Schema::rename('campaign_time_belts', 'campaign_mpo_time_belts');
        }
    }
}
