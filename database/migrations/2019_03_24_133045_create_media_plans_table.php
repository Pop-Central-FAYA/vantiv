<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan_id');
            $table->string('campaign_name');
            $table->string('product_name');
            $table->enum('gender', ['Male', 'Female', 'Both']);
            $table->string('client_name');
            $table->string('brand_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->double('total_budget');
            $table->double('actual_spend');
            $table->integer('total_target_reach');
            $table->integer('actual_reach');
            $table->string('lsms');
            $table->string('regions');
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
        Schema::dropIfExists('media_plans');
    }
}
