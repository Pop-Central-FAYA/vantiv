<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaPlanChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_plan_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('media_plan_id');
            $table->foreign('media_plan_id')->references('id')->on('media_plans');
            $table->enum('channel', ['Radio', 'Tv']);
            $table->double('budget');
            $table->integer('target_reach');
            $table->string('material_length');
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
        Schema::dropIfExists('media_plan_channels');
    }
}
