<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirDateToPreselectedAdslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('preselected_adslots', function (Blueprint $table) {
            $table->date('air_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('preselected_adslots', function (Blueprint $table) {
            $table->dropColumn('air_date');
        });
    }
}
