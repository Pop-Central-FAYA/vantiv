<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpotAmountToSelecetedAdslots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selected_adslots', function (Blueprint $table) {
            $table->integer('adslot_amount');
        });

        DB::statement(" UPDATE selected_adslots 
                    SET adslot_amount = (
                        SELECT CASE
                                    WHEN selected_adslots.`time_picked` = '15' THEN adslotPrices.price_15
                                    WHEN selected_adslots.`time_picked` = '30' THEN adslotPrices.price_30
                                    WHEN selected_adslots.`time_picked` = '45' THEN adslotPrices.price_45
                                                                                ELSE adslotPrices.price_60
                            END AS adslot_prices
                        FROM adslotPrices
                        WHERE adslotPrices.adslot_id = selected_adslots.`adslot`
                    )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selected_adslots', function (Blueprint $table) {
            $table->dropColumn('adslot_amount');
        });
    }
}
