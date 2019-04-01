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
            $table->string('id')->index();
            $table->string('external_user_id');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Others']);
            $table->string('region');
            $table->string('lsm');
            $table->string('state');
            $table->char('social_class');
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
