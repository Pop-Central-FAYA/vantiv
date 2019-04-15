<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToMediaPlanSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plan_suggestions', function (Blueprint $table) {
            $table->string('state');
            $table->string('region');
            $table->string('station_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_plan_suggestions', function (Blueprint $table) {
            if (Schema::hasColumn('media_plan_suggestions', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('media_plan_suggestions', 'region')) {
                $table->dropColumn('region');
            }
            if (Schema::hasColumn('media_plan_suggestions', 'station_type')) {
                $table->dropColumn('station_type');
            }
        });
    }
}
