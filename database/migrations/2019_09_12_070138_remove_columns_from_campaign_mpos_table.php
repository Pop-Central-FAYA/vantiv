<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromCampaignMposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_mpos', function (Blueprint $table) {
            $table->dropColumn([
                'station', 'ad_slots', 'status', 'budget'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_mpos', function (Blueprint $table) {
            $table->string('station');
            $table->integer('ad_slots');
            $table->string('status');
            $table->integer('budget');
        });
    }
}
