<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreselectedAdslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preselected_adslots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 25);
            $table->string('broadcaster_id', 25);
            $table->integer('price');
            $table->string('file_url');
            $table->integer('time');
            $table->string('from_to_time');
            $table->string('adslot_id', 25);
            $table->string('agency_id', 25)->nullable();
            $table->string('filePosition_id', 25)->nullable();
            $table->integer('percentage')->nullable();
            $table->integer('total_price');
            $table->string('file_name');
            $table->string('format');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index('user_id');
            $table->index('broadcaster_id');
            $table->index('adslot_id');
            $table->index('agency_id');
            $table->index('filePosition_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preselected_adslots');
    }
}
