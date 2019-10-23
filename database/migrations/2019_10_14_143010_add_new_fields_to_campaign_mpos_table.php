<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToCampaignMposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_mpos', function (Blueprint $table) {
            $table->string('requested_by')->index()->nullable();
            $table->string('approved_by')->index()->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
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
            $table->dropColumn(['requested_by', 'approved_by', 'requested_at', 'approved_at']);
        });
    }
}
