<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFakeTimebeltRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fake_time_belt_revenues', function (Blueprint $table) {
            $table->string('id');
            $table->string('station_id')->index();
            $table->string('day')->index();
            $table->time('start_time')->index();
            $table->time('end_time')->index();
            $table->decimal('revenue', 8, 2);
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
        Schema::dropIfExists('fake_time_belt_revenues');
    }
}
