<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpsAudiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mps_audiences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exact_age');
            $table->enum('gender', ['Male', 'Female', 'Others']);
            $table->string('region');
            $table->char('lsm');
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
        Schema::dropIfExists('mps_audiences');
    }
}
