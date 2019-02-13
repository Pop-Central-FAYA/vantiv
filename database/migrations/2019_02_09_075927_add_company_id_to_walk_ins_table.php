<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToWalkInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('walkIns', function (Blueprint $table) {
            $table->string('company_id', 25)->index();

            DB::query("update walkIns set company_id = broadcaster_id where broadcaster_id != ''");
            DB::query("update walkIns set company_id = agency_id where agency_id != ''");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('walkIns', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
