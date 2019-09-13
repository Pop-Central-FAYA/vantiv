<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE campaign_time_belts AS ctb
            JOIN campaign_mpos AS cm ON cm.id = ctb.mpo_id
            JOIN tv_stations AS ts ON ts.name = cm.station
            SET ctb.publisher_id = ts.publisher_id, ctb.campaign_id = cm.campaign_id
        ");   
    }
}
