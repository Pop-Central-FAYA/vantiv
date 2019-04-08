<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeBeltTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_belt_transactions', function (Blueprint $table) {
            $table->string('id',25);
            $table->string('time_belt_id', 25)->index();
            $table->string('media_program_id')->index();
            $table->string('media_program_name');
            $table->string('campaign_details_id', 25)->index();
            $table->integer('duration');
            $table->string('file_name');
            $table->string('file_url');
            $table->string('file_format');
            $table->integer('amount_paid');
            $table->date('playout_date')->index();
            $table->time('playout_hour');
            $table->string('approval_status');
            $table->string('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_belt_transactions');
    }
}
