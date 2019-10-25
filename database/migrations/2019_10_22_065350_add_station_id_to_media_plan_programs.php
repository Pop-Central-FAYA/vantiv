<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStationIdToMediaPlanPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plan_programs', function (Blueprint $table) {
            $table->string('station_id', 15)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_plan_programs', function (Blueprint $table) {
            $table->dropColumn('station_id');
        });
    }
}
