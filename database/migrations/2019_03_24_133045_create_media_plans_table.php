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
            $table->string('id')->index();
            $table->string('campaign_name')->nullable();
            $table->string('client_id')->reference('id')->on('walkIns')->nullable();
            $table->string('brand_id')->reference('id')->on('brands')->nullable();
            $table->string('product_name')->nullable();
            $table->double('budget')->default(0);
            $table->enum('criteria_gender', ['Male', 'Female', 'Both'])->nullable();
            $table->string('criteria_lsm')->nullable();
            $table->string('criteria_social_class')->nullable();
            $table->string('criteria_region')->nullable();
            $table->string('criteria_state')->nullable();
            $table->string('criteria_age_groups')->nullable();
            $table->double('agency_commission')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('planner_id')->reference('id')->on('users');
            $table->enum('status', ['Pending', 'Aproved', 'Declined']);
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
