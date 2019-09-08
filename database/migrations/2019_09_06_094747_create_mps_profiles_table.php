<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpsProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mps_profiles', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('ext_profile_id')->index();
            $table->string('wave');
            $table->integer('age')->index();
            $table->string('gender', 6);
            $table->string('region');
            $table->string('state');
            $table->char('social_class');
            $table->decimal('pop_weight', 10, 2);
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
        Schema::dropIfExists('mps_profiles');
    }
}
