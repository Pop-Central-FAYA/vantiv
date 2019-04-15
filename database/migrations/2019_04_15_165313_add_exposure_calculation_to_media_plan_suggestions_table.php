<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExposureCalculationToMediaPlanSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plan_suggestions', function (Blueprint $table) {
            $table->text('exposure_calculation');
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
            $table->dropColumn('exposure_calculation');
        });
    }
}
