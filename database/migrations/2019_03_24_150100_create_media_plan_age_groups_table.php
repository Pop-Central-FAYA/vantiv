<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaPlanAgeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_plan_age_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('media_plan_id');
            $table->foreign('media_plan_id')->references('id')->on('media_plans');
            $table->integer('min_age');
            $table->integer('max_age');
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
        Schema::dropIfExists('media_plan_age_groups');
    }
}
