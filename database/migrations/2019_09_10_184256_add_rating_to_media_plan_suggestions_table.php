<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingToMediaPlanSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plan_suggestions', function (Blueprint $table) {
            $table->decimal('rating', 5, 2);
            $table->string('station_id', 25)->index();
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
            $table->dropColumn('rating');
            $table->dropColumn('station_id');
        });
    }
}
