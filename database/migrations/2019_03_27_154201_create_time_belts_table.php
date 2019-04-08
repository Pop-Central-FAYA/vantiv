<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeBeltsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_belts', function (Blueprint $table) {
            $table->string('id', 25);
            $table->time('start_time')->index();
            $table->time('end_time')->index();
            $table->string('actual_time_picked')->nullable();
            $table->string('day');
            $table->string('media_program_id', 25)->index()->nullable();
            $table->string('station_id', 25)->index();
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
        Schema::dropIfExists('time_belts');
    }
}
