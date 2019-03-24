<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaPlanSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_plan_suggestions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('media_plan_id');
            $table->foreign('media_plan_id')->references('id')->on('media_plans');
            $table->string('station');
            $table->string('program');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('total_audience');
            $table->integer('total_spots');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('media_plan_suggestions');
    }
}
