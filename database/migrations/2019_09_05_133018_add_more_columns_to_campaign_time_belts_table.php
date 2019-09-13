<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColumnsToCampaignTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_time_belts', function (Blueprint $table) {
            $table->string('campaign_id', 25)->index();
            $table->string('publisher_id', 25)->index();
            $table->string('ad_vendor_id', 25)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_time_belts', function (Blueprint $table) {
            $table->dropColumn(['campaign_id', 'publisher_id', 'ad_vendor_id']);
        });
    }
}
