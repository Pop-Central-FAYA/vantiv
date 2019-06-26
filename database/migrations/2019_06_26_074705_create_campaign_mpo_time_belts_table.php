<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignMpoTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_mpo_time_belts', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('mpo_id')->reference('id')->on('campaign_mpos');
            $table->time('time_belt_start_time');
            $table->time('time_belt_end_date');
            $table->string('day');
            $table->integer('duration');
            $table->string('program');
            $table->integer('ad_slots');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_mpo_time_belts');
    }
}
