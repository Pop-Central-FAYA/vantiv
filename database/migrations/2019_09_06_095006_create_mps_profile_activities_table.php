<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpsProfileActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mps_profile_activities', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('ext_profile_id')->index();
            $table->string('wave');
            $table->enum('media_type', ['Radio', 'Tv']);
            $table->string('tv_station_id')->nullable()->index();
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
        Schema::dropIfExists('mps_profile_activities');
    }
}
