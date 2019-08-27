<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTvStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tv_stations', function (Blueprint $table) {
            $table->string('id', 25)->unique();
            $table->string('publisher_id', 25)->index();
            $table->string('name');
            $table->string('type');
            $table->string('state');
            $table->string('city');
            $table->string('region');
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
        Schema::dropIfExists('tv_stations');
    }
}