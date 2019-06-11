<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToTimeBeltTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_belt_transactions', function (Blueprint $table) {
            $table->string('company_id', 25)->index();
        });

        DB::statement(
            "update time_belt_transactions
                    set company_id =
                    (select launched_on
                    from campaignDetails
                    where campaignDetails.id = time_belt_transactions.`campaign_details_id`)"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_belt_transactions', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
