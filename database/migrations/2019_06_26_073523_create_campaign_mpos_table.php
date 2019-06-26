<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignMposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_mpos', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('campaign_id')->reference('id')->on('campaigns');
            $table->string('station');
            $table->integer('ad_slots');
            $table->string('status')->default('Submitted');
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
        Schema::dropIfExists('campaign_mpos');
    }
}
