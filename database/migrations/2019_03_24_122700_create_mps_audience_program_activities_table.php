<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpsAudienceProgramActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mps_audience_program_activities', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('mps_audience_id')->references('id')->on('mps_audiences');
            $table->enum('media_type', ['Radio', 'Tv']);
            $table->string('station');
            $table->string('program');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
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
        Schema::dropIfExists('mps_audience_program_activities');
    }
}
