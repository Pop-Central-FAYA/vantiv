<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdBreakToTimeBeltTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_belt_transactions', function (Blueprint $table) {
            $table->string('ad_break');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_belt_transactions', function (Blueprint $table) {
            $table->dropColumn('ad_break');
        });
    }
}
