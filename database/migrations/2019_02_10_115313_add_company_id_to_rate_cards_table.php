<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToRateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rateCards', function (Blueprint $table) {
            $table->string('company_id', 25)->index();
        });

        DB::statement("UPDATE rateCards set company_id = broadcaster");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rateCards', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
